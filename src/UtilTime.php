<?php

namespace Accuhit\BackendLibrary;

use DateTime;

class UtilTime
{
    public static function timeNow(): string
    {
        date_default_timezone_set("Asia/Taipei");
        $date = new DateTime("NOW");
        return $date->format("Y-m-d H:i:s.u");
    }
}
