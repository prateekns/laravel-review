<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Exceptions\NotificationFailedException;
use Throwable;

class TechnicianOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly string $otp
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['twilio'];
    }

    /**
     * Get the Twilio / SMS representation of the notification.
     */
    public function toTwilio(object $notifiable): array
    {
        return [
            'message' => __('api.otp.message', [
                'minutes' => 10
            ]),
            'to' => $notifiable->phone
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
            'phone' => $notifiable->phone,
            'type' => 'technician_otp'
        ];
    }

    /**
     * Handle a notification failure.
     *
     * @param Throwable $exception
     * @throws NotificationFailedException
     */
    public function failed(NotificationFailedException $exception): void
    {
        \Log::error('OTP notification failed', [
            'error' => $exception->getMessage(),
            'type' => get_class($exception)
        ]);

        throw new NotificationFailedException(
            'Failed to send OTP notification',
            previous: $exception
        );
    }
}
