<?php

namespace App\Support;

/**
 * Valores máximos que caben en las columnas de la BD. Validar contra ellos evita
 * que PostgreSQL rechace el INSERT/UPDATE con un error 500 ("value out of range").
 */
final class Limites
{
    /** Columnas `integer` (existencias, cantidad): máximo de un int4 de PostgreSQL. */
    public const ENTERO = 2147483647;

    /** Columnas `numeric(10,2)` (precio, total, montos): 8 enteros y 2 decimales. */
    public const DINERO = 99999999.99;

    /** Texto para los mensajes de error ("$99,999,999.99"). */
    public static function dineroTexto(): string
    {
        return '$'.number_format(self::DINERO, 2);
    }
}
