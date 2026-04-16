<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class NotificationAlert extends Component
{
    /**
     * Render the component
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.notification-alert');
    }
}
