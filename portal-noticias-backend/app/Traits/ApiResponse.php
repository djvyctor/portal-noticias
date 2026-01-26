<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    // retorna resposta de sucesso padronizada
    protected function successResponse(array $data = [], string $message = '', int $statusCode = 200): JsonResponse
    {
        $response = ['success' => true];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        
        return response()->json($response, $statusCode);
    }

    // retorna resposta de erro padronizada
    protected function errorResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }

    // retorna resposta não autorizada
    protected function unauthorizedResponse(string $message = 'Não autorizado.'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }
}
