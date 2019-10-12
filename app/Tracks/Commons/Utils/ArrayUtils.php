<?php

namespace App\Tracks\Commons\Utils;


class ArrayUtils
{

    /**
     * Comprueba si el primer elemento del array contiene alguno de los posiblesValores recibidos
     * @param $array
     * @param $posiblesValores
     * @return bool
     */
    public static function validaPrimerElemento($array, $posiblesValores)
    {
        if (count($array) > 0) {
            $primerElemento = $array[0];
            return in_array($primerElemento, $posiblesValores);
        }

        return false;
    }
}