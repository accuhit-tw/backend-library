<?php

namespace Accuhit\BackendLibrary;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use function Symfony\Component\String\s;

class UtilLogger
{
    /**
     * 紀錄log
     * storage/logs/{{method}}/{{Y-m-d}}.log
     */
    public static function putLogs(string $method, string $message): void
    {
        $dir = sprintf("%s/storage/logs/%s", $_SERVER['DOCUMENT_ROOT'] ?? '.', $method);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $log = sprintf("%s/%s.log", $dir, date("Y-m-d"));
        $message = sprintf("[%s] %s \n", Carbon::now()->format("Y-m-d H:i:s"), $message);
        file_put_contents($log, $message, FILE_APPEND);
    }
}
