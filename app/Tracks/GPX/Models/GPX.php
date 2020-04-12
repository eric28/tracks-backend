<?php /** @noinspection PhpUnusedAliasInspection */

namespace App\Tracks\GPX\Models;

use App\Tracks\Commons\BaseModel;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="string")
     */
    protected $centerJson;

    /**
     * @ORM\Column(type="text",length=16777215)
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
        $callback = function (TrackPoint $point) {
            return $point->toArray();
        };
        $this->name = $name;
        $this->centerJson = json_encode($centerJson->toArray());
        $this->gpxJson = json_encode(array_map($callback, $gpxJson));
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
        $center = json_decode($this->centerJson, true);

        return new TrackPoint($center["latitude"], $center["longitude"], $center["elevation"]);
    }

    /**
     * @param TrackPoint $centerJson
     */
    public function setCenterJson(TrackPoint $centerJson): void
    {
        $this->centerJson = json_encode($centerJson->toArray());
    }

    /**
     * @return mixed
     */
    public function getGpxJson()
    {
        $callback = function (array $point) {
            return new TrackPoint($point["latitude"], $point["longitude"], $point["elevation"]);
        };

        return array_map($callback, json_decode($this->gpxJson, true));
    }

    /**
     * @param mixed $gpxJson
     */
    public function setGpxJson(array $gpxJson): void
    {
        $callback = function (TrackPoint $point) {
            return $point->toArray();
        };
        $this->gpxJson = json_encode(array_map($callback, $gpxJson));
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
