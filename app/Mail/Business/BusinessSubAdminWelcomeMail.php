<?php

namespace App\Mail\Business;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Business\BusinessUser;
use Illuminate\Contracts\Queue\ShouldQueue;

class BusinessSubAdminWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $subAdmin;
    public $temporaryPassword;

    /**
     * Create a new message instance.
     *
     * @param BusinessUser $subAdmin
     * @param string $temporaryPassword
     * @return void
     */
    public function __construct(BusinessUser $subAdmin, string $temporaryPassword)
    {
        $this->subAdmin = $subAdmin;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to ' . config('app.name') . ' – Your Sub-admin Account Details')
                    ->view('emails.business.sub-admin-welcome');
    }
}
