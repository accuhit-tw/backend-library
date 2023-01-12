<?php

namespace Accuhit\BackendLibrary;

use Accuhit\BackendLibrary\Exceptions\UtilJwtException;
use Exception;

class UtilJwt
{
    private static string $secret;

    private static $instance;

    private function __construct()
    {
    }

    /**
     * @return UtilJwt
     */
    public static function getInstance(): UtilJwt
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$secret = env("JWT_SECRET", "secret");
        }
        return self::$instance;
    }

    public static function encode(array $payload, string $alg = 'SHA256'): string
    {
        $key = md5(self::$secret);
        $time = time();
        $arr = [
            'iss' => env('APP_NAME', "accuProject"), //簽發者
            'iat' => $time, //簽發時間
            'exp' => $time + env("JWT_EXP", 21600), //過期時間
            'nbf' => $time, //該時間之前不接收處理該Token
            'sub' => '', //面向用戶
            'jti' => md5(uniqid('JWT') . $time) //該token唯一認證
        ];
        $payload = array_merge($arr, $payload);

        $jwt = self::urlsafeB64Encode(json_encode(['typ' => 'JWT', 'alg' => $alg])) . '.' .
            self::urlsafeB64Encode(json_encode($payload));
        $signature = self::signature($jwt, $key, $alg);
        return $jwt . '.' . $signature;
    }

    /**
     * @param string $jwt
     * @return array
     * @throws UtilJwtException
     */
    public static function decode(string $jwt): array
    {
        $tokens = explode('.', $jwt);
        $key = md5(self::$secret);
        if (count($tokens) != 3) {
            throw new UtilJwtException("token error");
        }

        list($header64, $payload64, $sign) = $tokens;

        $header = json_decode(self::urlsafeB64Decode($header64), JSON_OBJECT_AS_ARRAY);
        if (empty($header['alg'])) {
            throw new UtilJwtException("alg error");
        }

        $signature = self::signature($header64 . '.' . $payload64, $key, $header['alg']);
        if ($signature !== $sign) {
            throw new UtilJwtException("signature error");
        }

        $payload = json_decode(self::urlSafeB64Decode($payload64), JSON_OBJECT_AS_ARRAY);

        $timeNow = $_SERVER['REQUEST_TIME'];
        if (isset($payload['iat']) && $payload['iat'] > $timeNow) {
            throw new UtilJwtException("iat error");
        }

        if (isset($payload['exp']) && $payload['exp'] < $timeNow) {
            throw new UtilJwtException("token expired");
        }

        return $payload;
    }

    /**
     * @param string $input
     * @return string
     */
    public static function urlSafeB64Decode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padLen = 4 - $remainder;
            $input .= str_repeat('=', $padLen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param string $input
     * @return string
     */
    public static function urlSafeB64Encode(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * @param string $input
     * @param string $key
     * @param string $alg
     * @return string
     */
    public static function signature(string $input, string $key, string $alg): string
    {
        try {
            return hash_hmac($alg, $input, $key);
        } catch (Exception $e) {
            return "";
        }
    }
}
