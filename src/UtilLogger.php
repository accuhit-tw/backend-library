<?php
/**
 * @author Alex <alex.hsu@accuhit.net>
 * @date 2022-12-14 Alex init.
 */

namespace Accuhit\BackendLibrary;

use Carbon\Carbon;

class UtilLogger
{
    /**
     * 紀錄log
     * @path storage/logs/{{method}}/{{Y-m-d}}.log
     * @discrapt 舊有流程會使用 UtilLogger::putLogs()
     */
    public static function putLogs(string $method, string $message): void
    {
        $path = env("LOG_DIR", sprintf("%s/storage/logs/", $_SERVER['DOCUMENT_ROOT'] ?? '.'));
        $dir = $path . strtolower($method);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $file = sprintf("%s/%s.log", $dir, date("Y-m-d"));
        $message = sprintf("[%s] %s \n", Carbon::now()->format("Y-m-d H:i:s"), $message);
        file_put_contents($file, $message, FILE_APPEND);
    }

    public static function info($message): void
    {
        self::putLogs("info", $message);
    }

    public static function debug($message): void
    {
        self::putLogs("debug", $message);
    }

    public static function warning($message): void
    {
        self::putLogs("warning", $message);
    }

    public static function error($message): void
    {
        self::putLogs("error", $message);
    }
}
