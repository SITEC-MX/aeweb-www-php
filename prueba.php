<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-13
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/demo/Get.php";
require_once __DIR__ . "/demo/Post.php";

$configuracion = array
(
    "aeweb_empresa" => "empresa",
    "aeweb_token" => "token-de-empresa",

    "base_url" => (empty($_SERVER["HTTPS"]) ? "http" : "https") . "://" . $_SERVER["HTTP_HOST"],

    "cache_directorio" => __DIR__ . "/.cache",
    "cache_habitilado" => $_ENV["CACHE_HABILITADO"] == "true"
);



$paginas = array
(
    "" => array("GET" => \Get::class, "POST"=> \Post::class),
);



$sitioweb = new \Mpsoft\AEWeb\www\SitioWeb($configuracion, $paginas);

$sitioweb->DesplegarPaginaActual($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], $_POST, $_GET);