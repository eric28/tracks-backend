<?php


namespace App\Tracks\GPX\Repositories;


use App\Models\GPX;
use App\Tracks\Commons\Repositories\GenericRepository;

class GPXRepository extends GenericRepository
{
    private $GPXModel;

    public function __construct(GPX $model)
    {
        parent::__construct($model);

        $this->GPXModel = $model;
    }

}