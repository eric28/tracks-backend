<?php


namespace App\Tracks\GPX\Utils;


use App\Tracks\Commons\Utils\CoordinatesHelper;
use App\Tracks\GPX\Models\TrackPoint;

class GpxDistancesCalculator
{
    public static function caculateDistanceInMeters(array $points): float
    {
        $distance = 0;

        /* @var TrackPoint $previous */
        $previous = null;

        /* @var TrackPoint $point */
        foreach ($points as $point) {
            if ($previous == null) {
                $previous = $point;
                continue;
            }
            $distance += CoordinatesHelper::distanceInMeters($previous->getLatitude(),
                $point->getLatitude(), $previous->getLongitude(), $point->getLongitude());
            $previous = $point;
        }


        return $distance;
    }

    public static function getUnevennessPositiveInMeters(array $points) : float
    {
        $unevenness = 0;

        /* @var TrackPoint $previous */
        $previous = null;

        /* @var TrackPoint $point */
        foreach ($points as $point) {
            if ($previous == null) {
                $previous = $point;
                continue;
            }
            if ($point->getElevation() > $previous->getElevation()) {
                $unevenness += $point->getElevation() - $previous->getElevation();
            }

            $previous = $point;
        }

        return $unevenness;
    }

}
