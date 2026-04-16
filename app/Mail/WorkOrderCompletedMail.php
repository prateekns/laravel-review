<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Business\WorkOrder\WorkOrder;

class WorkOrderCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $workOrder;
    public $technician;
    public $client;
    public $notes;

    /**
     * Create a new message instance.
     *
     * @param WorkOrder $workOrder
     * @param string|null $notes
     */
    public function __construct(WorkOrder $workOrder, ?string $notes = null)
    {
        $this->workOrder = $workOrder;
        $this->technician = $workOrder->technician;
        $this->client = $workOrder->client;
        $this->notes = $notes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Work Order ' . $this->workOrder->work_order_id . ' Completed')
                    ->view('emails.work_order_completed');
    }
}
