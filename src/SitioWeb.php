<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-09
 */
namespace Mpsoft\AEWeb\www;

use \Mpsoft\AEWeb\AEWeb;

use \Exception;
use \Throwable;

class SitioWeb
{
    private $openapi;

    private $configuracion;

    private $configuracion_por_defecto = array
    (
        "cache_directorio" => __DIR__ . "/.cache",
        "cache_habitilado" => FALSE
    );

    private $aeweb;

    public function __construct(array $configuracion, array $paginas)
    {
        $this->configuracion = $this->ConstruirConfiguracion($configuracion);
        $this->openapi = $this->ConstruirOpenAPI($paginas);

        $this->aeweb = new AEWeb($this->configuracion["aeweb_empresa"], $this->configuracion["aeweb_token"], "tienda");
    }

    private function ConstruirConfiguracion(array $configuracion): array
    {
        // Colocamos los valores por defecto en la configuración
        $configuracion_completa = array_merge($this->configuracion_por_defecto, $configuracion);

        $configuracion_obligatoria = array_flip(array("aeweb_empresa", "aeweb_token", "base_url"));
        $configuracion_faltante = array_diff_key($configuracion_obligatoria, $configuracion_completa);

        if(!empty($configuracion_faltante)) // Si no se proporciona una configuración obligatoria
        {
            $primer_configuracion_faltante = array_flip($configuracion_faltante)[0];

            throw new Exception("No se proporcionó la configuración '{$primer_configuracion_faltante}'");
        }

        return array_merge($this->configuracion_por_defecto, $configuracion);
    }

    private function ConstruirOpenAPI(array $paginas):array
    {
        $openapi = array();

        foreach($paginas as $url_enviada => $contenedor) // Para cada página proporcionada
        {
            // Preparamos la URL para incorporarse a OpenAPI
            $url_para_openapi = NULL;
            if(is_numeric($url_enviada)) // Si se envía un código de error
            {
                $url_para_openapi = $url_enviada;
            }
            else // Si se envía una URL
            {
                $ruta_para_url = str_replace("/", "\/", $url_enviada);
                $url_para_openapi = preg_replace('/\{(\w+)\}/', '(?<$1>[a-zA-Z0-9+_.-]+)', $ruta_para_url);
                $url_para_openapi = "/^{$url_para_openapi}$/U";                
            }

            $openapi[$url_para_openapi] = $contenedor;
        }

        return $openapi;
    }

    public function ObtenerAEWeb():AEWeb
    {
        return $this->aeweb;
    }

    public function ObtenerHelperDatos():HelperDatos
    {
        return new HelperDatos($this->aeweb);
    }

    private function DeterminarPaginaActual(string $REQUEST_METHOD, string $LLAMADASOLICITADA):array
    {
        $contenedor = NULL;
        $variables = array();

        // Determinamos qué llamada de la definición son posibles por la URL
        $pm_matches = NULL;
        foreach ($this->openapi as $url => $c) // Para cada llamada disponible
        {
            if (preg_match($url, $LLAMADASOLICITADA, $pm_matches) == 1) // Si encontramos la llamada solicitada
            {
                $contenedor = $c;

                // Preparamos las variables encontradas
                $variables = array_filter($pm_matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                break;
            }
        }

        $pagina_clase = NULL;
        if ($contenedor) // Si la página solicitada existe
        {
            if (isset($contenedor[$REQUEST_METHOD])) // Si el método solicitado está definido
            {
                $pagina_clase = $contenedor[$REQUEST_METHOD];
            }
            else // Si el método no está definido
            {
                if (isset($this->openapi["405"]) && isset($this->openapi["405"]["GET"])) // Si se define una página para el error 405
                {
                    $pagina_clase = $this->openapi["405"]["GET"];
                }
                else // Si no se define método GET para el error 405
                {
                    $pagina_clase = Pagina405::class;
                }
            }
        }
        else // Si la página solicitada no existe
        {
            if (isset($this->openapi["404"]) && isset($this->openapi["404"]["GET"])) // Si se define una página para el error 404
            {
                $pagina_clase = $this->openapi["404"]["GET"];
            }
            else // Si no se define método GET para el error 404
            {
                $pagina_clase = Pagina404::class;
            }
        }

        return array("clase"=> $pagina_clase, "variables"=> $variables) ;
    }

    private function DeterminarCacheKey(string $pagina_clase, string $LLAMADASOLICITADA, array $query_string):string
    {
        $clase = str_replace("\\", "_", $pagina_clase);

        $url = md5($LLAMADASOLICITADA);

        $querystring_str = http_build_query($query_string);
        $querystring = $querystring_str ? md5($querystring_str) : "";

        $cache_key = "{$clase}_{$url}_{$querystring}";

        return $cache_key;
    }

    public function DesplegarPaginaActual(string $REQUEST_METHOD, string $REQUEST_URI, array $body = array(), array $query_string = array()):void
    {
        /* Obtenemos la llamada solicitada */
        $LLAMADASOLICITADA = substr($REQUEST_URI, 1); // Sin / al inicio
        $INDICE_PARAMETRO = strpos($LLAMADASOLICITADA, "?"); // Quitamos los parámetros recibidos por QS
        if ($INDICE_PARAMETRO !== FALSE) // Si hay parámetros
        {
            $LLAMADASOLICITADA = substr($LLAMADASOLICITADA, 0, $INDICE_PARAMETRO);
        }

        $determinacion_pagina_actual = $this->DeterminarPaginaActual($REQUEST_METHOD, $LLAMADASOLICITADA);

        $pagina_clase = $determinacion_pagina_actual["clase"];
        $variables = $determinacion_pagina_actual["variables"];

        $content_type = $pagina_clase::ObtenerContentyType();
        $qs = $pagina_clase::ObtenerQueryString($pagina_clase, $query_string);
        
        $cache_ruta = NULL;
        if($REQUEST_METHOD == "GET" && $this->configuracion["cache_habitilado"]) // Si el método solicitado es GET y por lo tanto suceptible de tener cache
        {
            $cache_key = $this->DeterminarCacheKey($pagina_clase, $LLAMADASOLICITADA, $qs);

            $cache_ruta = $this->configuracion["cache_directorio"] . "/{$cache_key}";
            if (file_exists($cache_ruta)) // Si el archivo está disponible en cache
            {
                header("Content-Type: {$content_type}");
                readfile($cache_ruta);
                exit;
            }
        }

        $pagina = new $pagina_clase($this);

        $parametros = array
        (
            "base_url" => $this->configuracion["base_url"],
            "llamada_solicitada" => $LLAMADASOLICITADA,
            "variables" => $variables,
            "body" => $body,
            "qs" => $qs
        );

        $contenido = NULL;
        try // Intentamos desplegar la página solicitada
        {
            ob_start();
            $pagina->DesplegarPagina($parametros);
            $contenido = ob_get_contents();
            ob_end_clean();            
        }
        catch(PaginaException $pe) // En caso de error al desplegar la página
        {
            $codigo_de_error = $pe->ObtenerCodigoDeError();

            if (isset($this->openapi[$codigo_de_error]) && isset($this->openapi[$codigo_de_error]["GET"])) // Si se define una página página para el código de error
            {
                $pagina_clase = $this->openapi[$codigo_de_error]["GET"];
            }
            else // Si no se define método GET para el error 404
            {
                $pagina_clase = Pagina404::class;
            }

            try // Intentamos desplegar la página de error
            {
                $pagina = new $pagina_clase($this);

                ob_start();
                $pagina->DesplegarPagina($parametros);
                $contenido = ob_get_contents();
                ob_end_clean();
            }
            catch (Throwable $t) // En caso de error al desplegar la página de error
            {
                $pagina = new Pagina500($this);

                ob_start();
                $pagina->DesplegarPagina($parametros);
                $contenido = ob_get_contents();
                ob_end_clean();
            }
        }
        catch(Throwable $t)
        {
            $pagina = new Pagina500($this);

            ob_start();
            $pagina->DesplegarPagina($parametros);
            $contenido = ob_get_contents();
            ob_end_clean();
        }


        if($REQUEST_METHOD == "GET" && $this->configuracion["cache_habitilado"]) // Si el método solicitado es GET y por lo tanto suceptible de tener cache
        {
            file_put_contents($cache_ruta, $contenido);
        }

        header("Content-Type: {$content_type}");
        echo $contenido;
    }
}