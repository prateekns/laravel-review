<?php

namespace App\Models\Business\Chemical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Business\WorkOrder\WorkOrder;
use App\Models\Business\WorkOrder\WorkOrderAssignment;
use App\Models\Business\Technician\Technician;
use App\Models\Business\WorkOrder\UsedMaintenanceItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Business\CompletedJobCustomer;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChemicalLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'chemical_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'work_order_id',
        'instance_id',
        'chemical_id',
        'reading',
        'unit',
        'chemical_used',
        'qty_added',
        'chemical_used_unit',
        'range',
        'ideal_target',
        'tabs'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'reading' => 'decimal:2',
        'qty_added' => 'decimal:2',
        'tabs' => 'integer',
    ];

    /**
     * Scope: filter by instance id when provided.
     */
    public function scopeForInstance($query, ?int $instanceId)
    {
        if ($instanceId) {
            return $query->where('instance_id', $instanceId);
        }
        return $query;
    }

    /**
     * Get the work order that owns this chemical log.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    /**
     * Get the work order assignment that owns this chemical log.
     */
    public function workOrderAssignment(): BelongsTo
    {
        return $this->belongsTo(WorkOrderAssignment::class, 'instance_id', 'instance_id');
    }

    /**
     * Get the chemical for this log.
     */
    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class, 'chemical_id');
    }

    /**
     * Get the chemical used for this log.
     */
    public function chemicalUsed(): BelongsTo
    {
        return $this->belongsTo(ChemicalUsed::class, 'chemical_used_id');
    }

    /**
     * Get the technician that owns this chemical log.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }

    public function completedJobCustomer(): HasOne
    {
        return $this->hasOne(CompletedJobCustomer::class, 'instance_id', 'instance_id');
    }

    /**
     * Get the formatted reading attribute.
     */
    public function getFormattedReadingAttribute(): string
    {
        if ($this->reading === null) {
            return '-';
        }

        // Special handling for Free Chlorine with tabs
        if ($this->chemical_name === 'Free Chlorine(FAC)-ppm' && $this->tabs) {
            return $this->reading . '-Tab';
        }

        return (string) $this->reading;
    }

    /**
     * Get the formatted quantity added attribute.
     */
    public function getFormattedQtyAddedAttribute(): string
    {
        if ($this->qty_added === null) {
            return '-';
        }

        // Get unit from the chemicalUsed model, fallback to chemical model
        $unit = '';
        if ($this->chemical_used_unit) {
            $unit = $this->chemical_used_unit;
        }
        return (string) $this->qty_added . ($unit ? ' ' . $unit : '');
    }

    /**
     * Format a customer snapshot into a display name.
     */
    private function formatSnapshotName($snapshot): string
    {
        if (!$snapshot) {
            return '';
        }

        return trim(($snapshot->company_name ?: '') . ' ' . ($snapshot->first_name ?: '') . ' ' . ($snapshot->last_name ?: ''));
    }

    /**
     * Get customer name from work order.
     */
    private function getWorkOrderCustomerName(WorkOrder $workOrder): string
    {
        $currentInstanceId = $this->instance_id;

        if ($currentInstanceId && $workOrder->relationLoaded('completedJobCustomers')) {
            $snapshot = $workOrder->completedJobCustomers->firstWhere('instance_id', $currentInstanceId);
            $name = $this->formatSnapshotName($snapshot);
            if ($name !== '') {
                return $name;
            }
        }

        if ($workOrder->relationLoaded('customer') && $workOrder->customer) {
            return $workOrder->customer->customer_name;
        }

        return '';
    }

    /**
     * Get customer name from work order assignment.
     */
    private function getWorkOrderAssignmentCustomerName(WorkOrderAssignment $assignment): string
    {
        if (!$assignment->relationLoaded('completedJobCustomers')) {
            return '';
        }

        $snapshot = $assignment->completedJobCustomers->first();
        return $this->formatSnapshotName($snapshot);
    }

    /**
     * Accessor: compute the customer display name for this log via work order or completed snapshot.
     */
    public function getCustomerNameAttribute(): string
    {
        $workOrder = $this->workOrder;
        if ($workOrder) {
            $name = $this->getWorkOrderCustomerName($workOrder);
            if ($name !== '') {
                return $name;
            }
        }

        $workOrderAssignment = $this->workOrderAssignment;
        if ($workOrderAssignment) {
            return $this->getWorkOrderAssignmentCustomerName($workOrderAssignment);
        }

        return '';
    }

    public function usedMaintenanceItems(): HasMany
    {
        return $this->hasMany(UsedMaintenanceItem::class, 'instance_id', 'instance_id');
    }
}
