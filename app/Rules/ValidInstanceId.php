<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidInstanceId implements ValidationRule
{
    protected $workOrderId;

    public function __construct($workOrderId)
    {
        $this->workOrderId = $workOrderId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // #1 First check if work order exists and is recurring
        $workOrder = DB::table('work_orders')->where('id', $this->workOrderId)->first();

        if (!$workOrder) {
            $fail('The work order does not exist.');
            return;
        }

        if ($workOrder->is_recurring) {
            // #2 If work order is recurring, check instance_id in assignments
            $jobInstance = DB::table('work_order_assignments')
            ->where('instance_id', $value)
            ->where('work_order_id', $this->workOrderId)
            ->first();

            if (!$jobInstance) {
                $fail('Workorder instance is invalid or not completed.');
            }

        }
    }
}
