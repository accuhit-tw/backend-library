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
     * @return JsonResponse
     */
    public static function errorResponse(string $message = ""): JsonResponse
    {
        /** @var string $status */
        if (Validate::checkQueryStr($message)) {
            $message = "db error";
        }
        $response = [
            "msg" => $message
        ];
        switch ($message) {
            case "token is needed":
                $status = 401;
                break;
            case in_array($message, ["token error", "alg error", "signature error", "iat error", "user not found"]):
                $status = 403;
                break;
            case  "token expired":
                $status = 409;
                break;
            case  "db error":
                $status = 500;
                break;
            default:
                $status = 400;
                break;
        }
        return response()->json($response, $status);
    }
}
