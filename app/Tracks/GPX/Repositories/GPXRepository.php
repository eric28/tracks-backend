<?php


namespace App\Tracks\GPX\Repositories;


use App\Tracks\Commons\Repositories\BaseRepository;
use App\Tracks\GPX\Models\GPX;
use Doctrine\ORM\EntityManagerInterface;

class GPXRepository extends BaseRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, GPX::class);
    }
}
