<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearUserSession
{
    /**
     * Log out user from all devices by clearing their sessions
     */
    public function handle(int|array $userId): void
    {
        try {
            $currentSessionId = session()->getId();
            // Clear all sessions for this user except the current one
            if (is_array($userId)) {
                DB::table('sessions')
                    ->whereIn('user_id', $userId)
                    ->where('id', '!=', $currentSessionId)
                    ->delete();
            } else {
                DB::table('sessions')->where('user_id', $userId)->where('id', '!=', $currentSessionId)->delete();
            }

        } catch (\Exception $e) {
            // Log the error
            Log::warning('Failed to clear user sessions during password reset', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
