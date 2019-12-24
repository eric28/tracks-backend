<?php /** @noinspection PhpUnusedAliasInspection */


namespace App\Tracks\Commons;

use Illuminate\Contracts\Support\Arrayable;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Doctrine\ORM\Mapping AS ORM;


abstract class BaseModel implements Arrayable
{
    use Timestamps;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

}
