<?php

namespace App\Http\Swagger\V1;

/**
 * Class for Technician Profile API documentation
 */
class TechnicianProfileRoutes
{
    /**
     * @OA\Tag(
     *     name="Technician Profile",
     *     description="API Endpoints for technician profile management"
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/v1/technician/profile",
     *     operationId="getProfile",
     *     summary="Get authenticated technician's profile",
     *     tags={"Technician Profile"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="business", type="object"),
     *                 @OA\Property(property="skills", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function getProfile()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/technician/change-password",
     *     operationId="changePassword",
     *     summary="Change technician password",
     *     tags={"Technician Profile"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password"),
     *             @OA\Property(property="new_password", type="string", format="password"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password updated successfully")
     *         )
     *     )
     * )
     */
    public function changePassword()
    {
    }
}
