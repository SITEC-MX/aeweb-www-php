<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-13
 */

use \Mpsoft\AEWeb\AEWeb;

class Post extends \Mpsoft\AEWeb\www\Pagina
{
    public function __construct(\Mpsoft\AEWeb\www\SitioWeb $sitioweb)
    {
        parent::__construct($sitioweb);

        $this->http_codigo_de_estado = 200;
    }

    public function EscribirContenido(array $parametros): void
    {
        $n1 = isset($parametros["body"]["n1"]) ? $parametros["body"]["n1"] : 0;
        $n2 = isset($parametros["body"]["n2"]) ? $parametros["body"]["n2"] : 0;

        $resultado = array("suma"=>($n1 + $n2));

        echo json_encode($resultado);
    }



    public static function ObtenerContentyType(): string
    {
        return "application/json";
    }

    protected function CargarDatos(array $parametros): array
    {
        return array();
    }

    public static function DefinicionQueryString(): array
    {
        return array();
    }

    public static function DefinicionBody(): array
    {
        return array
        (
            "n1" => array("tipo" => AEWeb::DATO_INT),
            "n2" => array("tipo" => AEWeb::DATO_INT)
        );
    }
}