<?php

namespace Accuhit\BackendLibrary;

use Illuminate\Http\JsonResponse;

class UtilResponse
{
    /**
     * @param int $statusCode
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    public static function toJson(int $statusCode = 200, string $message = "", array $data = []): JsonResponse
    {
        $response = [
            "msg" => $message,
            "data" => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    public static function successResponse(string $message, array $data = []): JsonResponse
    {
        $response = [
            "msg" => $message
        ];
        if (!empty($data)) {
            $response["data"] = $data;
        }
        return response()->json($response);
    }

    /**
     * @param string $message
     * @param array $data
     * @param string $code
     * @return JsonResponse
     */
    public static function errorResponse(string $message = "", array $data = [], string $errorCode = "", int $statusCode = 400): JsonResponse
    {
        /** @var string $statusCode */
        if (Validate::checkQueryStr($message)) {
            UtilLogger::error($message);
            $message = "db error";
        }
        $response = [
            "msg" => $message
        ];
        if (!empty($data)) {
            $response["data"] = $data;
        }
        if (!empty($errorCode)) {
            $response["code"] = $errorCode;
        }
        switch ($message) {
            case "token is needed":
                $statusCode = 401;
                break;
            case in_array($message, ["token error", "alg error", "signature error", "iat error", "user not found"]):
                $statusCode = 403;
                break;
            case "token expired":
                $statusCode = 409;
                break;
            case "db error":
                $statusCode = 500;
                break;
            default:
                $statusCode = 400;
                break;
        }
        return response()->json($response, $statusCode);
    }
}
