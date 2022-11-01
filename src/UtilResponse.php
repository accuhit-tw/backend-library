<?php

namespace Accuhit\BackendLibrary;

use Illuminate\Http\JsonResponse;

class UtilResponse
{

    public static function toJson(int $statusCode = 200, string $message = "", array $data = []): JsonResponse
    {
        $response = [
            "msg" => $message,
            "data" => $data
        ];
        return response()->json($response, $statusCode);
    }

    public static function successResponse($message, $data = []): JsonResponse
    {
        $response = [
            "msg" => $message
        ];
        if (!empty($data)) {
            $response["data"] = $data;
        }
        return response()->json($response);
    }

    public static function errorResponse(string $message = ""): JsonResponse
    {
        $status = 400;
        if (Validate::checkQueryStr($message)) {
            $message = "db is error";
        }
        $response = [
            "msg" => $message
        ];
        switch ($message) {
            case "tokens is needed":
                $status = 401;
                break;
            case in_array($message, ["tokens is error", "alg is error", "signature is error", "iat is error", "user is not found"]):
                $status = 403;
                break;
            case  "token is expired":
                $status = 409;
                break;
            case  "db is error":
                $status = 500;
                break;
        }
        return response()->json($response, $status);
    }
}
