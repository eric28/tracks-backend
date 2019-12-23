<?php

namespace App\Tracks\GPX\Services;


use App\Tracks\Commons\Utils\Base64Utils;
use App\Tracks\GPX\Exceptions\GPXInvalidNameException;
use App\Tracks\GPX\Exceptions\GPXRouteNotGeneratedException;
use App\Tracks\GPX\Models\GPX;
use App\Tracks\GPX\Models\TrackPoint;
use App\Tracks\GPX\Repositories\GPXRepository;
use App\Tracks\GPX\Utils\GpxDistancesCalculator;
use Exception;
use LaravelDoctrine\ORM\Facades\EntityManager;
use SimpleXMLElement;

class GPXService
{
    private $GPXRepository;

    /**
     * ListingGpx constructor.
     * @param GPXRepository $GPXRepository
     */
    public function __construct(GPXRepository $GPXRepository)
    {
        $this->GPXRepository = $GPXRepository;
    }

    public function listGPX($perPage, $page)
    {
        $data = $this->GPXRepository->paginateAll($perPage, $page);

        return $data->toArray();
    }

    /**
     * @param $name
     * @param $gpxB64
     * @return bool|int
     * @throws GPXInvalidNameException
     * @throws GPXRouteNotGeneratedException
     * @throws GPXNotSavedException
     */
    public function addGPX($name, $gpxB64)
    {
        if (strlen($name) == 0) throw new GPXInvalidNameException("Se debe indicar un nombre para la ruta");

        try {
            $xmlGpx = Base64Utils::decode(Base64Utils::getB64Data($gpxB64));
            $gpx = simplexml_load_string($xmlGpx);

            $route = [];
            foreach ($this->getChildsXml(["trk", "trkseg"], $gpx) as $pt) {
                $elevation = $this->getChildsXml(["ele"], $pt);
                $route[] = [
                    'lat' => (string)$pt['lat'],
                    'lon' => (string)$pt['lon'],
                    'elevation' => (string)$elevation
                ];
            }
        } catch (Exception $e) {
            throw new GPXRouteNotGeneratedException("No se ha podido leer el fichero GPX");
        }

        if (count($route) == 0) throw new GPXRouteNotGeneratedException("No se ha podido leer el fichero GPX");

        $center = $this->GetCenterFromDegrees($route);

        $points = [];
        foreach ($route as $routePoint) {
            $points[] = new TrackPoint(floatval($routePoint["lat"]), floatval($routePoint["lon"]),
                floatval($routePoint["elevation"]));
        }

        $centerPoint = new TrackPoint(floatval($center["lat"]), floatval($center["lon"]), 0.0);

        $unevennessPositive = GpxDistancesCalculator::getUnevennessPositiveInMeters($points);
        $distance = GpxDistancesCalculator::caculateDistanceInMeters($points);

        $gpx = new GPX($name, $centerPoint, $points, $distance, $unevennessPositive);

        $gpx = $this->GPXRepository->persist($gpx);

        if (is_null($gpx->getId())) throw new GPXNotSavedException("No se ha podido guardar el GPX");

        return $gpx->getId();
    }

    public function removeGPX($id)
    {
        $gpx = $this->GPXRepository->find($id);

        return $this->GPXRepository->remove($gpx);
    }

    /**
     * @param $tags
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement
     */
    private function getChildsXml($tags, $xml)
    {
        $tag = $tags[0];

        foreach ($xml->children() as $xmlChild) {
            if ($xmlChild->getName() == $tag) {
                if (count($tags) == 1) return $xmlChild;
                array_shift($tags);
                return $this->getChildsXml($tags, $xmlChild);
            }
        }

        return $xml;
    }

    private function GetCenterFromDegrees($data)
    {
        if (!is_array($data)) return FALSE;

        $num_coords = count($data);

        $X = 0.0;
        $Y = 0.0;
        $Z = 0.0;

        foreach ($data as $coord) {
            $lat = $coord["lat"] * pi() / 180;
            $lon = $coord["lon"] * pi() / 180;

            $a = cos($lat) * cos($lon);
            $b = cos($lat) * sin($lon);
            $c = sin($lat);

            $X += $a;
            $Y += $b;
            $Z += $c;
        }

        $X /= $num_coords;
        $Y /= $num_coords;
        $Z /= $num_coords;

        $lon = atan2($Y, $X);
        $hyp = sqrt($X * $X + $Y * $Y);
        $lat = atan2($Z, $hyp);

        return array("lat" => $lat * 180 / pi(), "lon" => $lon * 180 / pi());
    }
}
