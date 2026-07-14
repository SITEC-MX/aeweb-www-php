<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-10
 */
namespace Mpsoft\AEWeb\www;

class Pagina500 extends Pagina
{
    public function __construct(SitioWeb $sitioweb)
    {
        parent::__construct($sitioweb);

        $this->http_codigo_de_estado = 404;
    }

    public function EscribirContenido(array $parametros): void
    {
        ?>
<html>
    <head><title>500</title></head>
    <body>
        <h1>500 - Error interno en el servidor</h1>
        <hr />
        <p>aeweb</p>
    </body>
</html>
<?php
    }



    public static function ObtenerContentyType(): string
    {
        return "text/html";
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
        return array();
    }
}
