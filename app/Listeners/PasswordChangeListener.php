<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use App\Actions\ClearUserSession;

class PasswordChangeListener
{
    /**
     * Handle the password changed event.
     *
     * @param PasswordChanged $event
     * @return void
     */
    public function handle(PasswordChanged $event): void
    {
        $clearUserSession = new ClearUserSession();
        $clearUserSession->handle($event->user->id);
    }
}
