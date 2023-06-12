<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse(string $message = '', $data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse(string $message = '', int $code = 400): JsonResponse
    {
        return response()->json(['message' => $message], $code);
    }

    protected function notAuthorizedResponse(): JsonResponse
    {
        return response()->json(['message' => 'You are not authorized to perform this action'], 403);
    }
}
