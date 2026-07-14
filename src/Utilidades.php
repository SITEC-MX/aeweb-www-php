<?php
/**
 * Sistemas Especializados e Innovación Tecnológica, SA de CV
 * SiTEC AE - Administrador de Empresas Web
 *
 * v.1.0.0.0 - 2026-07-13
 */
namespace Mpsoft\AEWeb\www;

use \Mpsoft\AEWeb\AEWeb;

use \DateTime;
use \Exception;

abstract class Utilidades
{
    public static function ConvertirATipoDeDato(mixed $valor, int $tipoDeDato): mixed
    {
        switch ($tipoDeDato)
        {
            case AEWeb::DATO_BOOL:
                $valor = filter_var($valor, FILTER_VALIDATE_BOOLEAN);
                break;

            case AEWeb::DATO_INT:
                $valor = $valor !== null ? (int) $valor : null;
                break;

            case AEWeb::DATO_FLOAT:
            case AEWeb::DATO_DOUBLE:
                $valor = $valor !== null ? floatval($valor) : null;
                break;

            case AEWeb::DATO_STRING:
                if (!is_null($valor)) // Si hay algún valor que convertir
                {
                    if (!is_string($valor)) // Si el valor no es string
                    {
                        $tipoVariable = gettype($valor);
                        switch ($tipoVariable)
                        {
                            case "boolean":
                                $valor = $valor ? "true" : "false";
                                break;

                            case "double":
                            case "integer":
                                $valor = (string) $valor;
                                break;

                            case "object":
                                if (is_a($valor, DateTime::class)) // Si el valor no es un DateTime
                                {
                                    $valor = $valor->format("Y-m-d H:i:s");
                                    break;
                                }
                                else // Si el valor no es un DateTime
                                {
                                    throw new Exception("El tipo de objeto de origen '" . get_class($valor) . "' no está soportado.");
                                }
                                break;

                            default:
                                throw new Exception("El tipo de dato de origen '{$tipoVariable}' no está soportado.");
                        }
                    }
                }
                break;

            case AEWeb::DATO_DATE:
            case AEWeb::DATO_DATETIME:
            case AEWeb::DATO_TIME:
                if (!is_null($valor)) // Si hay algún valor que convertir
                {
                    if (!is_a($valor, DateTime::class)) // Si el valor no es un DateTime
                    {
                        if (is_string($valor)) // Si el valor es string
                        {
                            $valor = new DateTime($valor);

                            if ($tipoDeDato == AEWeb::DATO_DATE) // Si se está convirtiendo a fecha
                            {
                                $valor->setTime(0, 0, 0, 0);
                            }
                        }
                        else // Si el valor no es string
                        {
                            throw new Exception("No es posible convertir el valor a Date o DateTime");
                        }
                    }
                }
                break;
        }

        return $valor;
    }
}