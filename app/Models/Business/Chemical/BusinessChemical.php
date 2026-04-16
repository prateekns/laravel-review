<?php

namespace App\Models\Business\Chemical;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessChemical extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'business_chemicals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'business_id',
        'chemical_id',
        'ideal_target',
        'range'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'ideal_target' => 'decimal:2',
    ];

    /**
     * Get the business that owns this chemical.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the chemical for this business chemical.
     */
    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class);
    }
}
