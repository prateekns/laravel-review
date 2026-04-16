<?php

namespace App\Models\Business\WorkOrder;

use App\Models\Business\Technician\Technician;
use Illuminate\Database\Eloquent\Model;

class WorkOrderPosition extends Model
{
    protected $fillable = [
        'work_order_id',
        'technician_id',
        'scheduled_date',
        'position',
        'instance_id'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }
}
