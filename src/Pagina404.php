<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-09
 */
namespace Mpsoft\AEWeb\www;

class Pagina404 extends Pagina
{
    public function __construct(SitioWeb $sitioweb)
    {
        parent::__construct($sitioweb);

        $this->http_codigo_de_estado = 404;
    }

    public function EscribirHTML(array $parametros): void
    {
?>
<html>
    <head><title>404</title></head>
    <body>
        <h1>404 - No encontrado</h1>
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
}
