<?php

namespace Accuhit\BackendLibrary;

use Illuminate\Support\Facades\File;

class UtilLogger
{
    /**
     * 紀錄log
     * private/storage/log/{{git 專案名稱}}/{{method}}/{{Y-m-d}}.log
     */
    public static function putLogs(string $method, string $message)
    {
        $log = base_path('private/log/') . env('APP_NAME') . '/' . $method;
        if (!File::isDirectory($log)) {
            File::makeDirectory($log, 0777, true); //mkdir 0777
        }
        $log .= '/' . date("Y-m-d") . '.log';
        $timeNow = "[" . UtilTime::timeNow() . "]";
        file_put_contents($log, $timeNow . " " . $message . "\n", FILE_APPEND);
    }
}
