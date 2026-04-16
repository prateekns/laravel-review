<?php

namespace App\Http\Swagger;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Pool Platform API Documentation",
 *     description="API documentation for Pool Platform Technician API",
 *     @OA\Contact(
 *         email="support@poolplatform.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="sanctum",
 *     description="Laravel Sanctum token authentication"
 * )
 */
class OpenApiSpec
{
}
