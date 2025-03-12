<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Devuelve una respuesta formateada con código de retorno, glosa de retorno, timestamp y respuesta.
     *
     * @param int $codigoRetorno El código de retorno de la operación.
     * @param string $glosaRetorno La glosa o mensaje de retorno.
     * @param mixed $respuesta La respuesta adicional (opcional).
     * @return array La respuesta formateada.
     */
    public static function returnResponse(int $codigoRetorno, string $glosaRetorno, mixed $respuesta = [])
    {
        return [
            "codigoRetorno" => $codigoRetorno,
            "glosaRetorno"  => $glosaRetorno,
            "timestamp"     => new \DateTime('NOW'),
            "respuesta"     => $respuesta,
        ];
    }
}
