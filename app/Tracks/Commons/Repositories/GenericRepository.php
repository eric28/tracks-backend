<?php

namespace App\Tracks\Commons\Repositories;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GenericRepository
{
    const PARAMETRO_COLUMNAS = "field";
    const PARAMETRO_ORDEN = "sort";
    const PARAMETRO_INCLUDES = "include";

    /**
     * @var Model|Builder
     */
    protected $model;

    /**
     * @param Model $model
     */
    function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $id
     * @param array
     * @return array
     */
    public function getById($id, $columns = array('*'))
    {
        $model = $this->model->find($id, $columns);
        return isset($model) ? $model->toArray() : null;
    }

    public function findByCriteria($criteria, $columns = array('*'), $skip = null, $limit = 15,
                                   $orderColumn = array(array('id', "asc")), $includes = [])
    {
        $result = $this->model->select($columns);

        foreach ($includes as $include) $result->with($include);

        foreach ($orderColumn as $order) $result->orderBy($order[0], $order[1]);

        if (is_array($criteria) && !empty($criteria)) RepositoyFilterGenerator::filterModel($result, $criteria);

        if (is_numeric($skip)) $result->skip($skip);

        if (is_numeric($limit)) $result->take($limit);

        return $result->get()->toArray();
    }

    public function countByCriteria($criteria)
    {
        $result = $this->model->select(['*']);

        if (is_array($criteria) && !empty($criteria)) RepositoyFilterGenerator::filterModel($result, $criteria);

        return $result->count();
    }

    /**
     * @param array
     * @return bool|int
     */
    public function add(array $data)
    {
        $model = $this->model->create($data);
        return isset($model) ? $model->id : false;
    }

    /**
     * @param $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        $model = $this->model->find($id);
        if (!isset($model)) return false;
        return $model->update($data);
    }

    /**
     * @param $id
     * @return bool
     */
    public function remove($id)
    {
        $modelo = $this->model->find($id);

        if (!isset($modelo)) return false;

        try {
            return $modelo->delete();
        } catch (\Exception $e) {
            return false;
        }
    }
}