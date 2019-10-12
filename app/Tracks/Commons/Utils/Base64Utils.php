<?php

namespace App\Tracks\Commons\Utils;

class Base64Utils
{
    public static function getB64Extension($b64File){
        $fileInfo = self::getB64Info($b64File);
        $formatDocument = explode(':', explode(';', $fileInfo)[0])[1];

        if (strpos($formatDocument, 'image') !== false)
            return explode("/", $formatDocument)[1];

        switch (explode("/", $formatDocument)[1]) {
            case "vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                return "xlsx";
                break;
            case "vnd.ms-excel":
                return "xls";
                break;
            default:
                return null;
        }
    }

    public static function decode($b64Data)
    {
        return base64_decode($b64Data);
    }

    public static function getB64Data($b64File)
    {
        return explode(',', $b64File)[1];
    }

    public static function getB64Info($b64File)
    {
        return explode(',', $b64File)[0];
    }

    public static function encode($data)
    {
        return base64_encode($data);
    }
}