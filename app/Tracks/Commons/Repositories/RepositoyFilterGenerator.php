<?php

namespace App\Tracks\Commons\Repositories;


use App\Tracks\Commons\Utils\ArrayUtils;
use Illuminate\Database\Eloquent\Builder;

class RepositoyFilterGenerator
{
    public static function filterModel($query, $filtros)
    {
        $operador = $filtros[0];
        $campos = $filtros[1];
        self::filterModelRecursive($query, $campos, $operador);
    }

    /**
     * @param Builder $query
     * @param $order
     */
    public static function orderModel($query, $order)
    {
        foreach ($order as $orderColumn)
            $query->orderBy($orderColumn['column'], $orderColumn['direction']);
    }

    /**
     * @param Builder $query
     * @param $filtros
     * @param $operadorPadre
     */
    private static function filterModelRecursive($query, $filtros, $operadorPadre)
    {
        $primerElementoComparador = ArrayUtils::validaPrimerElemento($filtros, ['and', 'or']);

        if ($primerElementoComparador) {
            $operador = $filtros[0];
            $campos = $filtros[1];

            $query->where(function ($q1) use ($operador, $campos) {
                $q1->where(function ($q2) use ($operador, $campos) {
                    self::filterModelRecursive($q2, $campos, $operador);
                }, null, null, $operador);
            }, null, null, $operadorPadre);

        } else if (!$primerElementoComparador && count($filtros) == 3 && is_string($filtros[1])) {
            switch ($operadorPadre) {
                case 'or':
                    self::comparaOr($query, $filtros);
                    break;
                case 'and':
                    self::compara($query, $filtros);
            }
        } else {
            foreach ($filtros as $filtro) self::filterModelRecursive($query, $filtro, $operadorPadre);
        }
    }

    private static function compara($query, $filtros)
    {
        self::comparacion($query, $filtros, 'and');
    }

    private static function comparaOr($query, $filtros)
    {
        self::comparacion($query, $filtros, 'or');
    }

    /**
     * @param Builder $query
     * @param $comparacion
     * @param $tipoWhere
     */
    private static function comparacion($query, $comparacion, $tipoWhere)
    {
        $columna = $comparacion[0];
        $comparador = $comparacion[1];
        $valor = $comparacion[2];

        if (is_string($valor)) $valor = addslashes($valor);
        elseif (is_array($valor))
            foreach ($valor as $k => $v) if (!is_array($v))$valor[$k] = addslashes($v);

        switch ($comparador) {
            case 'in':
                if (count($valor) > 1)
                    $query->where(function ($subQuery) use ($columna, $comparador, $valor) {
                        $subQuery->whereIn($columna, $valor);
                        if (in_array('null', $valor)) $subQuery->orWhereNull($columna);
                    }, null, null, $tipoWhere);
                else
                    $query->where($columna, '=', $valor, $tipoWhere);
                break;
            case 'not in':
                if (count($valor) > 1)
                    $query->where(function ($subQuery) use ($columna, $comparador, $valor) {
                        $subQuery->whereNotIn($columna, $valor);
                        if (in_array('null', $valor)) $subQuery->orWhereNotNull($columna);
                    }, null, null, $tipoWhere);
                else
                    $query->where($columna, '<>', $valor, $tipoWhere);
                break;
            case 'eq':
                if ($valor === 'null')
                    $query->whereNull($columna, $tipoWhere);
                else
                    $query->where($columna, '=', $valor, $tipoWhere);
                break;
            case 'ne':
                if ($valor === 'null')
                    $query->whereNotNull($columna, $tipoWhere);
                else
                    $query->where($columna, '!=', $valor, $tipoWhere);
                break;
            case 'gt':
                $query->where($columna, '>', $valor, $tipoWhere);
                break;
            case 'gte':
                $query->where($columna, '>=', $valor, $tipoWhere);
                break;
            case 'lt':
                $query->where($columna, '<', $valor, $tipoWhere);
                break;
            case 'lte':
                $query->where($columna, '<=', $valor, $tipoWhere);
                break;
            case 'like':
                $query->where($columna, 'LIKE', '%' . $valor . '%', $tipoWhere);
                break;
            case 'likeIn':
                $query->where(function ($subQuery) use ($columna, $comparador, $valor) {
                    foreach ($valor as $v)
                        $subQuery->orWhere($columna, 'LIKE', '%' . $v . '%');
                }, null, null, $tipoWhere);
                break;
            case 'likeAll':
                $query->where(function ($subQuery) use ($columna, $comparador, $valor) {
                    foreach ($valor as $v)
                        $subQuery->where($columna, 'LIKE', '%' . $v . '%');
                }, null, null, $tipoWhere);
                break;
            case 'inYear':
                $query->where(function ($query) use ($columna, $valor) {
                    foreach ($valor as $anyo) {
                        $query->orWhere(function ($q) use ($columna, $anyo) {
                            $q->where($columna, '>=', $anyo . '-01-01 00:00:00');
                            $q->where($columna, '<=', $anyo . '-12-31 23:59:59');
                        });
                    }
                }, null, null, $tipoWhere);
                break;
            case 'raw':
                $query->whereRaw($valor, [], $tipoWhere);
                break;
        }
    }
}