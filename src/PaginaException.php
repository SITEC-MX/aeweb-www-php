<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-09
 */
namespace Mpsoft\AEWeb\www;

use \Exception;

class PaginaException extends Exception
{
    protected $codigo_de_error;

    public function __construct(string $mensaje, int $codigo_de_error)
    {
        parent::__construct($mensaje);

        $this->codigo_de_error = $codigo_de_error;
    }

    public function ObtenerCodigoDeError():int
    {
        return $this->codigo_de_error;
    }
}