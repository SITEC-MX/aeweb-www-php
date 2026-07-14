<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-13
 */

class Get extends \Mpsoft\AEWeb\www\Pagina
{
    public function __construct(\Mpsoft\AEWeb\www\SitioWeb $sitioweb)
    {
        parent::__construct($sitioweb);

        $this->http_codigo_de_estado = 200;
    }

    public function EscribirContenido(array $parametros): void
    {
?><html>
    <head><title>Suma de dos números</title></head>
    <body>
    <form method="post">
        <label for="num1">Número 1:</label>
        <input type="number" id="n1" name="n1" required>
        <br><br>
        
        <label for="num2">Número 2:</label>
        <input type="number" id="n2" name="n2" required>
        <br><br>
        
        <button type="submit">Sumar</button>
    </form>
    </body>
</html><?php
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