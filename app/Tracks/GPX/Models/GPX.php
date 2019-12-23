<?php /** @noinspection PhpUnusedAliasInspection */

namespace App\Tracks\GPX\Models;

use App\Tracks\Commons\BaseModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gpx")
 */
class GPX extends BaseModel
{
    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var TrackPoint
     * @ORM\OneToOne(targetEntity="TrackPoint", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="center_point_id", referencedColumnName="id")
     */
    protected $centerJson;

    /**
     * @var array
     * @ORM\ManyToMany(targetEntity="TrackPoint", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="gpx_track_points",
     *      joinColumns={@ORM\JoinColumn(name="track_point_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="gpx_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $gpxJson;

    /**
     * @ORM\Column(type="float", precision=4, scale=4)
     */
    protected $distance;

    /**
     * @ORM\Column(type="float", precision=4, scale=4)
     */
    protected $unevennessPositive;

    /**
     * GPX constructor.
     * @param $name
     * @param TrackPoint $centerJson
     * @param array $gpxJson
     * @param float $distance
     * @param float $unevennessPositive
     */
    public function __construct($name, TrackPoint $centerJson, array $gpxJson, float $distance,
                                float $unevennessPositive)
    {
        $this->name = $name;
        $this->centerJson = $centerJson;
        $this->gpxJson = new ArrayCollection($gpxJson);
        $this->distance = $distance;
        $this->unevennessPositive = $unevennessPositive;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return TrackPoint
     */
    public function getCenterJson(): TrackPoint
    {
        return $this->centerJson;
    }

    /**
     * @param TrackPoint $centerJson
     */
    public function setCenterJson(TrackPoint $centerJson): void
    {
        $this->centerJson = $centerJson;
    }

    /**
     * @return mixed
     */
    public function getGpxJson()
    {
        return $this->gpxJson;
    }

    /**
     * @param mixed $gpxJson
     */
    public function setGpxJson($gpxJson): void
    {
        $this->gpxJson = $gpxJson;
    }

    /**
     * @return mixed
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param mixed $distance
     */
    public function setDistance($distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return mixed
     */
    public function getUnevennessPositive()
    {
        return $this->unevennessPositive;
    }

    /**
     * @param mixed $unevennessPositive
     */
    public function setUnevennessPositive($unevennessPositive): void
    {
        $this->unevennessPositive = $unevennessPositive;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $points = [];
        /* @var TrackPoint $p */
        foreach ($this->getGpxJson() as $p) {
            $points[] = $p->toArray();
        }

        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "center_json" => $this->getCenterJson()->toArray(),
            "gpx_json" => $points,
            "unevenness_positive" => $this->getUnevennessPositive(),
            "distance" => $this->getDistance()
        ];
    }
}
