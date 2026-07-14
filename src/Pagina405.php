<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-09
 */
namespace Mpsoft\AEWeb\www;

class Pagina405 extends Pagina
{
    public function __construct(SitioWeb $sitioweb)
    {
        parent::__construct($sitioweb);

        $this->http_codigo_de_estado = 405;
    }

    public function EscribirContenido(array $parametros): void
    {
?>
<html>
    <head><title>405</title></head>
    <body>
        <h1>405 - Método no soportado</h1>
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
