<?php

namespace App\Http\Swagger\V1;

/**
 * Class for Technician Authentication API documentation
 */
class TechnicianAuthRoutes
{
    /**
     * @OA\Tag(
     *     name="Technician Authentication",
     *     description="API Endpoints for technician authentication"
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/v1/technician/get-signature",
     *     operationId="generateSignature",
     *     summary="Generate signature for device authentication",
     *     description="Generates a signature that must be used in subsequent authentication requests via X-App-Signature header",
     *     tags={"Technician Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"device_id", "timestamp"},
     *             @OA\Property(property="device_id", type="string", example="device123"),
     *             @OA\Property(property="timestamp", type="integer", example=1753905508)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Signature generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Signature retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(
     *                     property="signature",
     *                     type="string",
     *                     example="0b1026ade900f6a08766a769d8f98558be1743624c07c56fd76cfb5a25bdaeec"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function generateSignature()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/technician/login",
     *     operationId="technicianLogin",
     *     summary="Login a technician",
     *     description="Authenticate a technician. Requires signature from /get-signature endpoint in X-App-Signature header",
     *     tags={"Technician Authentication"},
     *     @OA\Parameter(
     *         name="X-App-Signature",
     *         in="header",
     *         required=true,
     *         description="Signature obtained from /get-signature endpoint",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="X-App-Timestamp",
     *         in="header",
     *         required=true,
     *         description="Current Unix timestamp in seconds. Must be within 5 minutes of server time.",
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="X-Device-Id",
     *         in="header",
     *         required=true,
     *         description="Device ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"staff_id", "password"},
     *             @OA\Property(property="staff_id", type="string", example="TECH123"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="technician", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid signature or credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid signature")
     *         )
     *     )
     * )
     */
    public function login()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/technician/password/reset-request",
     *     operationId="requestOtp",
     *     summary="Request OTP for login",
     *     description="Request OTP for password reset. Requires signature from /get-signature endpoint in X-App-Signature header",
     *     tags={"Technician Authentication"},
     *     @OA\Parameter(
     *         name="X-App-Signature",
     *         in="header",
     *         required=true,
     *         description="Signature obtained from /get-signature endpoint",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"staff_id"},
     *             @OA\Property(property="staff_id", type="string", example="TECH123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="phone_last_four", type="string", example="*****12345")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid signature",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid signature")
     *         )
     *     )
     * )
     */
    public function otpLoginRequest()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/technician/password/verify-reset-otp",
     *     operationId="verifyOtp",
     *     summary="Verify OTP for login",
     *     description="Verify OTP for password reset. Requires signature from /get-signature endpoint in X-App-Signature header",
     *     tags={"Technician Authentication"},
     *     @OA\Parameter(
     *         name="X-App-Signature",
     *         in="header",
     *         required=true,
     *         description="Signature obtained from /get-signature endpoint",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"staff_id", "otp"},
     *             @OA\Property(property="staff_id", type="string", example="TECH123"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid signature or OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid signature")
     *         )
     *     )
     * )
     */
    public function doOtpLogin()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/technician/auth/invalidate",
     *     operationId="logout",
     *     summary="Logout the authenticated technician",
     *     tags={"Technician Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout()
    {
    }
}
