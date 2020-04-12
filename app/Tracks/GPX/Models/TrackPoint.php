<?php

namespace App\Tracks\GPX\Models;

class TrackPoint
{
    private $latitude;
    private $longitude;
    private $elevation;

    /**
     * TrackPoint constructor.
     * @param $latitude
     * @param $longitude
     * @param $elevation
     */
    public function __construct($latitude, $longitude, $elevation)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->elevation = $elevation;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getElevation()
    {
        return $this->elevation;
    }

    /**
     * @param mixed $elevation
     */
    public function setElevation($elevation): void
    {
        $this->elevation = $elevation;
    }


    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            "latitude" => $this->getLatitude(),
            "longitude" => $this->getLongitude(),
            "elevation" => $this->getElevation()
        ];
    }
}
