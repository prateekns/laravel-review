<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordResetTokenCheck
{
    /**
     * Check if the password reset token is expired
     */
    public function handle(?string $token, ?string $email, ?string $guard = null): bool
    {
        $isExpired = true;

        if (!empty($token) && !empty($email)) {
            $passwordResetTable = $guard ? 'password_reset_tokens' : 'business_password_resets';
            $tokenRecord = DB::table($passwordResetTable)
                ->where('email', $email)
                ->first();

            if ($tokenRecord && Hash::check($token, $tokenRecord->token)) {
                $expiration = config('auth.passwords.users.expire', 60);
                $tokenAge = now()->diffInMinutes($tokenRecord->created_at);
                $isExpired = $tokenAge > $expiration;
            }
        }

        return $isExpired;
    }
}
