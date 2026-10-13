<?php

namespace App\Domain\Shared\Http;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(array $data, array $meta = []): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => 'Success', 'errors' => null, 'meta' => (object) $meta]);
    }
}
