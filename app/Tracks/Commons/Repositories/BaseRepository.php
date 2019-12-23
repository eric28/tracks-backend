<?php


namespace App\Tracks\Commons\Repositories;


use App\Tracks\Commons\BaseModel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class BaseRepository extends EntityRepository
{
    use PaginatesFromParams;

    public function __construct(EntityManagerInterface $em, $class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    public function persist(BaseModel $model)
    {
        try {
            $this->getEntityManager()->persist($model);
            $this->getEntityManager()->flush();

            return $model;
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        }

        return null;
    }

    public function remove($model)
    {
        try {
            $this->getEntityManager()->remove($model);
            $this->getEntityManager()->flush();
            return true;
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        }
        return false;
    }
}
