<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-09
 */
namespace Mpsoft\AEWeb\www;

use \Mpsoft\AEWeb\AEWeb;

abstract class Pagina
{
    protected $http_codigo_de_estado;

    protected $sitioweb;

    public function __construct(\Mpsoft\AEWeb\www\SitioWeb $sitioweb)
    {
        $this->http_codigo_de_estado = 200;

        $this->sitioweb = $sitioweb;
    }

    public function DesplegarPagina(array $parametros): void
    {
        http_response_code($this->http_codigo_de_estado);

        $parametros["datos"] = $this->CargarDatos($parametros);

        $this->EscribirContenido($parametros);
    }



    public static function ObtenerQueryString(string $clase_pagina, array $query_string):array
    {
        $query_string_definicion = $clase_pagina::DefinicionQueryString();

        $qs = array();
        foreach ($query_string_definicion as $nombre=>$definicion) // Para cada parámetro definido en la definición de query string
        {
            if( isset($query_string[$nombre]) ) // Si se proporciona el parámetro
            {
                // Lo convertimos al tipo de datos especificado
                $tipo = isset($definicion["tipo"]) ? $definicion["tipo"] : AEWeb::DATO_STRING;

                $valor = Utilidades::ConvertirATipoDeDato($query_string[$nombre], $tipo);

                if($tipo == AEWeb::DATO_STRING) // Si el tipo de dato es string
                {
                    $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
                }

                $qs[$nombre] = $valor;
            }
            else // Si no se proporciona el parámetro
            {
                if( isset($definicion["default"]) ) // Si se proporciona un valor por defecto
                {
                    $qs[$nombre] = $definicion["default"];
                }
            }
        }

        return $qs;
    }

    public static function ObtenerBody(string $clase_pagina, array $body): array
    {
        $body_definicion = $clase_pagina::DefinicionBody();

        $bd = array();
        foreach ($body_definicion as $nombre => $definicion) // Para cada parámetro definido en la definición de body
        {
            if (isset($body[$nombre])) // Si se proporciona el parámetro
            {
                // Lo convertimos al tipo de datos especificado
                $tipo = isset($definicion["tipo"]) ? $definicion["tipo"] : AEWeb::DATO_STRING;

                $valor = Utilidades::ConvertirATipoDeDato($body[$nombre], $tipo);

                if ($tipo == AEWeb::DATO_STRING) // Si el tipo de dato es string
                {
                    $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
                }

                $bd[$nombre] = $valor;
            }
        }

        return $bd;
    }



    protected abstract function EscribirContenido(array $parametros): void;

    protected abstract function CargarDatos(array $parametros): array;
        


    public abstract static function ObtenerContentyType(): string;

    public abstract static function DefinicionQueryString(): array;

    public abstract static function DefinicionBody(): array;
}
