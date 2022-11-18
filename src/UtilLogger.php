<?php

namespace Accuhit\BackendLibrary;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class UtilLogger
{
    /**
     * 紀錄log
     * storage/logs/{{method}}/{{Y-m-d}}.log
     */
    public static function putLogs(string $method, string $message): void
    {
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/storage/logs/' . $method;
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0775, true);
        }
        $log = sprintf("%s/%s.log", $dir, date("Y-m-d"));
        $message = sprintf("[%s] %s \n", Carbon::now()->format("Y-m-d H:i:s"), $message);
        file_put_contents($log, $message, FILE_APPEND);
    }
}
