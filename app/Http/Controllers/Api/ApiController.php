<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

abstract class ApiController extends Controller
{

    protected function sendError(int $code = 400, string $content = 'error', string $message = 'error'): \Illuminate\Http\JsonResponse
    {
        return response($content, $code)->json([
            'success' => false,
            'message' => $message
        ]);
    }
    protected function sendResponse(int $code = 200, array $body = [], string $content = 'success'): \Illuminate\Http\JsonResponse
    {
        return response($content, $code)->json(array_merge(['success' => true], $body));
    }
}
