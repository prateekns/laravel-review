<?php

namespace App\Models\Business\WorkOrder;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderChecklistItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'work_order_id',
        'business_id',
        'instance_id',
        'description',
        'is_completed',
        'is_visible',
        'is_custom',
        'is_default',
        'sort_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_completed' => 'boolean',
        'is_visible' => 'boolean',
        'is_custom' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the work order that owns the checklist item
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the business that owns the checklist item
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
