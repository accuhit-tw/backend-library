<?php

namespace Accuhit\BackendLibrary;

use Exception;

class UtilJwt
{
	private static string $privateKey;
	private static string $publicKey;
	private static string $secretKey;

	private static ?UtilJwt $instance = null;

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
            self::$secretKey = getenv("JWT_SECRET") ?: 'secret';
			self::$privateKey = file_get_contents(__DIR__ . '/keys/jwt_private.pem');
			self::$publicKey = file_get_contents(__DIR__ . '/keys/jwt_public.pem');
        }
        return self::$instance;
    }

	/**
	 * @throws Exception
	 */
	public static function encode(array $payload, string $alg = 'SHA256'): string
    {
		if (!in_array($alg, ['SHA256', 'RS256'])) {
			throw new \InvalidArgumentException("Invalid algorithm specified.");
		}

		$time = time();
        $arr = [
            'iss' => getenv('APP_NAME') ?: 'accuProject', //簽發者
            'iat' => $time, //簽發時間
            'exp' => $time + (getenv("JWT_EXP") ?: 21600), //過期時間
            'nbf' => $time, //該時間之前不接收處理該Token
            'sub' => '', //面向用戶
            'jti' => md5(uniqid('JWT') . $time) //該token唯一認證
        ];
        $payload = array_merge($arr, $payload);

        $jwt = self::urlsafeB64Encode(json_encode(['typ' => 'JWT', 'alg' => $alg])) . '.' .
            self::urlsafeB64Encode(json_encode($payload));

		$signature = self::signature($jwt,
			$alg === 'RS256' ? self::$privateKey : md5(self::$secretKey),
			$alg,
			true);

		return $jwt . '.' . $signature;
    }

    /**
     * @param string $jwt
     * @return array
     * @throws Exception
	 */
    public static function decode(string $jwt): array
    {
		$tokens = explode('.', $jwt);
		if (count($tokens) != 3) {
			throw new \Exception("token error");
		}

		list($header64, $payload64, $sign) = $tokens;

		$header = json_decode(self::urlsafeB64Decode($header64), JSON_OBJECT_AS_ARRAY);
		if (empty($header['alg'])) {
			throw new \Exception("alg error");
		}

		$isValid = self::signature($jwt,
			$header['alg'] === 'RS256' ? self::$publicKey : md5(self::$secretKey),
			$header['alg'],
			false);

		if (!$isValid) {
			throw new \Exception("signature error");
		}

		$payload = json_decode(self::urlSafeB64Decode($payload64), JSON_OBJECT_AS_ARRAY);

		$timeNow = $_SERVER['REQUEST_TIME'];
		if (isset($payload['iat']) && $payload['iat'] > $timeNow) {
			throw new \Exception("iat error");
		}

		if (isset($payload['exp']) && $payload['exp'] < $timeNow) {
			throw new \Exception("token expired");
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
	 * @param  string  $input
	 * @param  string  $key
	 * @param  string  $alg
	 * @param  bool  $isSigning
	 * @return string
	 * @throws Exception
	 */
	public static function signature(string $input, string $key, string $alg, bool $isSigning = true): string
	{
		try {
			if ($alg === 'RS256') {
				if ($isSigning) {
					openssl_sign($input, $signature, $key, OPENSSL_ALGO_SHA256);
					return self::urlSafeB64Encode($signature);
				} else {
					$tokens = explode('.', $input);
					list($header64, $payload64, $sign) = $tokens;
					return openssl_verify($header64 . '.' . $payload64,
							self::urlsafeB64Decode($sign),
							$key,
							OPENSSL_ALGO_SHA256) === 1;
				}
			}else{
				if ($isSigning){
					return hash_hmac($alg, $input, $key);
				}else{
					$tokens = explode('.', $input);
					list($header64, $payload64, $sign) = $tokens;
					return hash_hmac($alg, $header64 . '.' . $payload64, $key) === $sign;
				}
			}
		} catch (Exception $e) {
			return "";
		}
	}

}
