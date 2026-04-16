<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    /**
     * Success response with optional resource and message
     */
    protected function successResponse(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        $response = [
            'success'  => true,
            'code' => $status,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Use a resource and add meta info
     */
    protected function resourceResponse(JsonResource $resource, string $message = 'Success', int $status = 200): JsonResource
    {
        return $resource->additional([
            'code' => $status,
            'message' => $message,
        ]);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message = '', int $status = 200, mixed $error = null): JsonResponse
    {
        $errorResponse = [
            'success'  => false,
            'code' => $status,
            'message' => $message,
        ];

        if ($error) {
            $errorResponse['error_code'] = $error;
        }

        return response()->json($errorResponse, $status);
    }
}
