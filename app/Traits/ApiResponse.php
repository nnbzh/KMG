<?php


namespace App\Traits;


use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse($data, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status'=> true,
            'message' => $message,
            'data' => $data
        ], $code, [], JSON_UNESCAPED_UNICODE);
    }

    protected function errorResponse($code, $message = null, $data = null): JsonResponse
    {
        return response()->json([
            'status'=>false,
            'message' => $message,
            'data' => $data
        ], $code, [], JSON_UNESCAPED_UNICODE);
    }
}
