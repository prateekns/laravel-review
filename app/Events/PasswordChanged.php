<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Admin\User;
use App\Models\Business\BusinessUser;

class PasswordChanged
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param BusinessUser|User $user
     * @return void
     */
    public function __construct(public BusinessUser|User $user)
    {
    }
}
