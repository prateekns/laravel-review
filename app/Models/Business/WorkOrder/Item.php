<?php

namespace App\Models\Business\WorkOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Business\Business;

class Item extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'business_id',
    ];

    /**
     * Get the business that owns the item.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
