<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-10
 */
namespace Mpsoft\AEWeb\www;

use \Mpsoft\AEWeb\AEWeb;

class HelperDatos
{
    private $aeweb;

    public function __construct(AEWeb $aeweb)
    {
        $this->aeweb = $aeweb;
    }



    private function ObtenerDatos(string $entidad, array $campos, int $inicio, int $numero_de_registros, ?array $filtros = NULL, ?array $ordenamiento = NULL, ?int &$total_de_registros = NULL, ?int $imagen_tipo = NULL, ?int $imagen_tamano = NULL): ?array
    {
        $body = array();
        $body["inicio"] = $inicio;
        $body["registros"] = $numero_de_registros;
        $body["campos"] = $campos;

        if ($filtros)
        {
            $body["filtro"] = $filtros;
        }

        if ($ordenamiento)
        {
            $body["ordenamiento_campos"] = $ordenamiento;
        }

        if ($imagen_tipo || $imagen_tamano) // Si se proporciona configuración de imágenes
        {
            $body["imagen"] = array("tipo" => $imagen_tipo, "tamano" => $imagen_tamano);
        }

        $estado = NULL;
        switch ($entidad)
        {
            case "HREFLANGS":
                $estado = $this->aeweb->POST_TiendaHreflangsQuery(NULL, NULL, $body);
                break;
            case "PRESENTACIONES":
                $estado = $this->aeweb->POST_InventarioPresentacionesQuery(NULL, NULL, $body);
                break;
            case "CATEGORIAS":
                $estado = $this->aeweb->POST_InventarioCategoriasQuery(NULL, NULL, $body);
                break;
            case "MARCAS":
                $estado = $this->aeweb->POST_InventarioMarcasQuery(NULL, NULL, $body);
                break;
            case "PRECIOS":
                $estado = $this->aeweb->POST_ContabilidadPrecioQuery(NULL, NULL, $body);
                break;
            case "EXISTENCIAS":
                $estado = $this->aeweb->POST_InventarioExistenciaQuery(NULL, NULL, $body);
                break;
        }

        $registros = NULL;
        if ($estado["estado"] == AEWeb::OK) // Éxito al obtener los datos
        {
            $registros = $estado["resultado"]["registros"];
            $total_de_registros = $estado["resultado"]["filtrados"];
        }

        return $registros;
    }

    public function ObtenerHreflangs(array $campos, int $inicio, int $numero_de_registros, ?array $filtros = NULL, ?array $ordenamiento = NULL, ?int &$total_de_registros = NULL): ?array
    {
        return $this->ObtenerDatos("HREFLANG", $campos, $inicio, $numero_de_registros, $filtros, $ordenamiento, $total_de_registros);
    }

    public function ObtenerPresentaciones(array $campos, int $inicio, int $numero_de_registros, ?array $filtros = NULL, ?array $ordenamiento = NULL, ?int &$total_de_registros = NULL, ?int $imagen_tipo = NULL, ?int $imagen_tamano = NULL): ?array
    {
        // Agregamos los filtos que siempre deberían proporcionarse
        if (!$filtros) // Si no se proporcionan filtros
        {
            $filtros = array();
        }

        $filtros[] = array("campo" => "activo", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => 1);
        $filtros[] = array("campo" => "publicado", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => 1);

        return $this->ObtenerDatos("PRESENTACIONES", $campos, $inicio, $numero_de_registros, $filtros, $ordenamiento, $total_de_registros, $imagen_tipo, $imagen_tamano);
    }

    public function ObtenerCategorias(array $campos, int $inicio, int $numero_de_registros, ?array $filtros = NULL, ?array $ordenamiento = NULL, ?int &$total_de_registros = NULL): ?array
    {
        // Agregamos los filtos que siempre deberían proporcionarse
        if (!$filtros) // Si no se proporcionan filtros
        {
            $filtros = array();
        }

        $filtros[] = array("campo" => "activo", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => 1);
        $filtros[] = array("campo" => "publicado", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => 1);

        return $this->ObtenerDatos("CATEGORIAS", $campos, $inicio, $numero_de_registros, $filtros, $ordenamiento, $total_de_registros);
    }

    public function ObtenerMarcas(array $campos, int $inicio, int $numero_de_registros, ?array $filtros = NULL, ?array $ordenamiento = NULL, ?int &$total_de_registros = NULL, ?int $imagen_tipo = NULL, ?int $imagen_tamano = NULL): ?array
    {
        // Agregamos los filtos que siempre deberían proporcionarse
        if (!$filtros) // Si no se proporcionan filtros
        {
            $filtros = array();
        }

        $filtros[] = array("campo" => "activo", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => 1);
        $filtros[] = array("campo" => "publicado", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => 1);

        return $this->ObtenerDatos("MARCAS", $campos, $inicio, $numero_de_registros, $filtros, $ordenamiento, $total_de_registros, $imagen_tipo, $imagen_tamano);
    }

    public function ObtenerPrecios(array $campos, int $inicio, int $numero_de_registros, ?array $filtros = NULL, ?array $ordenamiento = NULL, ?int &$total_de_registros = NULL): ?array
    {
        return $this->ObtenerDatos("PRECIOS", $campos, $inicio, $numero_de_registros, $filtros, $ordenamiento, $total_de_registros);
    }

    public function ObtenerExistencias(array $campos, int $inicio, int $numero_de_registros, ?array $filtros = NULL, ?array $ordenamiento = NULL, ?int &$total_de_registros = NULL): ?array
    {
        return $this->ObtenerDatos("EXISTENCIAS", $campos, $inicio, $numero_de_registros, $filtros, $ordenamiento, $total_de_registros);
    }



    public function ObtenerElemento(string $entidad, array $campos, array $filtros): ?array
    {
        $imagen_tipo = AEWeb::IMAGENOPTIMIZADA_TIPO_ORIGINAL;
        $imagen_tamano = AEWeb::IMAGENOPTIMIZADA_TAMANO_GRANDE;

        $registros = NULL;
        switch ($entidad)
        {
            case "HREFLANG":
                $registros = $this->ObtenerHreflangs($campos, 1, 1, $filtros);
                break;
            case "PRESENTACION":
                $registros = $this->ObtenerPresentaciones($campos, 1, 1, $filtros, NULL, $total_registros, $imagen_tipo, $imagen_tamano);
                break;
            case "CATEGORIA":
                $registros = $this->ObtenerCategorias($campos, 1, 1, $filtros);
                break;
            case "MARCA":
                $registros = $this->ObtenerMarcas($campos, 1, 1, $filtros);
                break;
        }

        $elemento = null;
        if ($registros) // Éxito al obtener el registro
        {
            $elemento = $registros[0];
        }

        return $elemento;
    }

    public function ObtenerHrefLang(string $hreflang): ?array
    {
        $filtros = array
        (
            array("campo" => "url", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => $hreflang)
        );

        return $this->ObtenerElemento("HREFLANG", array("id", "nombre", "url"), $filtros);
    }

    public function ObtenerPresentacion(string $presentacion_url):?array
    {
        $filtros = array
        (
            array("campo" => "url", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => $presentacion_url)
        );

        return $this->ObtenerElemento("PRESENTACION", array("id", "codigo", "producto_nombre", "nombre", "producto_resumen", "producto_informacion", "imagenprincipal_url", "marca_nombre", "marca_url", "url"), $filtros);
    }

    public function ObtenerCategoria(string $categoria_url): ?array
    {
        $filtros = array
        (
            array("campo" => "url", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => $categoria_url)
        );

        return $this->ObtenerElemento("CATEGORIA", array("id", "nombre", "url"), $filtros);
    }

    public function ObtenerMarca(string $marca_url): ?array
    {
        $filtros = array
        (
            array("campo" => "url", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => $marca_url)
        );

        return $this->ObtenerElemento("MARCA", array("id", "nombre", "url"), $filtros);
    }




    public function ObtenerProductosConCategoria(int $categoria_id): array
    {
        $variables = array("id" => $categoria_id);

        $estado = $this->aeweb->GET_InventarioCategoriasProductos($variables);

        $producto_ids = array();
        if ($estado["estado"] == AEWeb::OK) // Éxito al obtener los productos con la categoría
        {
            $producto_ids = $estado["resultado"];
        }

        return $producto_ids;
    }

    public function ObtenerPresentacionesConPrecioExistencia(array $campos, int $inicio, int $numero_de_registros, ?array $filtros = NULL, ?array $ordenamiento = NULL, ?int &$total_de_registros = NULL): ?array
    {
        $presentaciones = $this->ObtenerPresentaciones
        (
            $campos, // Campos
            $inicio, // Inicio
            $numero_de_registros, // Número de registros
            $filtros, // Filtros
            $ordenamiento, // Ordenamiento
            $total_de_registros // Conteo de registros
        );

        // Obtenemos los precios
        $presentacion_ids = array();
        foreach ($presentaciones as $presentacion)
        {
            $presentacion_ids[] = $presentacion["id"];
        }
        $presentacion_ids_str = implode(",", $presentacion_ids);

        $precios = $this->ObtenerPrecios
        (
            array("presentacion_id", "precio"), // Campos
            1, // Inicio
            1000, // Número de registros
            array // Filtros
            (
                array("campo" => "presentacion_id", "operador" => AEWeb::OPERADOR_IN, "valor" => $presentacion_ids_str),
                array("campo" => "esquemaprecio_id", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => 1)
            ),
            NULL, // Ordenamiento
        );

        $precios_presentacion = array();
        foreach ($precios as $precio) // Para cada precio
        {
            $presentacion_id = $precio["presentacion_id"];

            $precios_presentacion[$presentacion_id] = $precio["precio"];
        }

        // Obtenemos las existencias
        $existencias = $this->ObtenerExistencias
        (
            array("presentacion_id", "existencia"), // Campos
            1, // Inicio
            1000, // Número de registros
            array // Filtros
            (
                array("campo" => "presentacion_id", "operador" => AEWeb::OPERADOR_IN, "valor" => $presentacion_ids_str),
                array("campo" => "almacen_id", "operador" => AEWeb::OPERADOR_IGUAL, "valor" => 1)
            ),
            NULL, // Ordenamiento
        );

        $existencias_presentacion = array();
        foreach ($existencias as $existencia) // Para cada precio
        {
            $presentacion_id = $existencia["presentacion_id"];

            $existencias_presentacion[$presentacion_id] = $existencia["existencia"];
        }

        // Inyectamos la información
        foreach ($presentaciones as $indice => $presentacion) // Para cada presentación
        {
            $presentacion_id = $presentacion["id"];

            $precio = isset($precios_presentacion[$presentacion_id]) ? $precios_presentacion[$presentacion_id] : NULL;
            $existencia = isset($existencias_presentacion[$presentacion_id]) ? $existencias_presentacion[$presentacion_id] : 0;

            $presentaciones[$indice]["precio"] = $precio;
            $presentaciones[$indice]["existencia"] = $existencia;
        }

        return $presentaciones;
    }
}