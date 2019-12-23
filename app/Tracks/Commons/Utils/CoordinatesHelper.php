<?php


namespace App\Tracks\Commons\Utils;


class CoordinatesHelper
{
    public static function distanceInMeters(float $lat1, float $lat2, float $lon1, float $lon2)
    {
        $earthRadius = 6371000; //meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (float) ($earthRadius * $c);
    }
}
