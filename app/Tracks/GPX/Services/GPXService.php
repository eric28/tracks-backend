<?php

namespace App\Tracks\GPX\Services;


use App\Models\GPX;
use App\Tracks\Commons\Utils\Base64Utils;
use App\Tracks\GPX\Exceptions\GPXInvalidNameException;
use App\Tracks\GPX\Exceptions\GPXRouteNotGeneratedException;
use App\Tracks\GPX\Repositories\GPXRepository;
use Exception;
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

    public function listGPX($start, $length, $sort)
    {
        if (is_null($sort)) $sort = GPX::COLUMN_ID;

        $order = [[
            str_replace_first('-', '', $sort),
            starts_with($sort, '-') ? "desc" : "asc"
        ]];

        $data = $this->GPXRepository->findByCriteria([], ['*'], $start, $length, $order);

        $total = $this->GPXRepository->countByCriteria([]);

        $dataProcess = [];
        foreach ($data as $dat) {
            $dat[GPX::COLUMN_GPX_JSON] = json_decode($dat[GPX::COLUMN_GPX_JSON]);
            $dat[GPX::COLUMN_CENTER] = json_decode($dat[GPX::COLUMN_CENTER]);
            $dataProcess[] = $dat;
        }
        return [
            'data' => $dataProcess,
            'total' => $total,
        ];
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

        $data = [
            GPX::COLUMN_NAME => $name,
            GPX::COLUMN_GPX_JSON => json_encode($route),
            GPX::COLUMN_CENTER => json_encode($center),
        ];

        $id = $this->GPXRepository->add($data);

        if ($id === false) throw new GPXNotSavedException("No se ha podido guardar el GPX");

        return $id;
    }

    public function removeGPX($id)
    {
        return $this->GPXRepository->remove($id);
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
