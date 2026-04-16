<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\Api\TwilioService;
use Illuminate\Support\Facades\Log;
use App\Exceptions\NotificationFailedException;

class TwilioChannel
{
    /**
     * Create a new Twilio channel instance.
     */
    public function __construct(
        private readonly TwilioService $twilioService
    ) {
    }

    /**
     * Send the given notification.
     *
     * @throws NotificationFailedException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toTwilio')) {
            throw new NotificationFailedException(
                'Notification class must implement toTwilio method'
            );
        }

        $message = $notification->toTwilio($notifiable);

        try {
            $response = $this->twilioService->sendMessage(
                $message['to'],
                $message['message']
            );

            Log::info('Twilio notification sent successfully', ['message' => $response]);

        } catch (\Throwable $e) {
            throw new NotificationFailedException($e->getMessage());
        }
    }
}
