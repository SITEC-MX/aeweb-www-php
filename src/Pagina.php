<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-09
 */
namespace Mpsoft\AEWeb\www;

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

        $this->EscribirHTML($parametros);
    }



    public static function ObtenerQueryString(string $clase_pagina, array $query_string):array
    {
        $query_string_definicion = $clase_pagina::DefinicionQueryString();

        $qs = array();
        foreach ($query_string_definicion as $nombre=>$definicion) // Para cada parámetro definido en la definición de query string
        {
            if( isset($query_string[$nombre]) ) // Si se proporciona el parámetro
            {
                $qs[$nombre] = $query_string[$nombre];
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



    protected abstract function EscribirHTML(array $parametros): void;

    protected abstract function CargarDatos(array $parametros): array;
        


    public abstract static function ObtenerContentyType(): string;

    public abstract static function DefinicionQueryString(): array;
}
