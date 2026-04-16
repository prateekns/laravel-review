<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Notifications\Channels\TwilioChannel;
use App\Services\Api\TwilioService;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->afterResolving(ChannelManager::class, function (ChannelManager $manager): void {
            $manager->extend('twilio', function ($app): TwilioChannel {
                return new TwilioChannel(
                    $app->make(TwilioService::class)
                );
            });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
