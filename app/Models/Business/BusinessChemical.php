<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessChemical extends Model
{
    public const DECIMAL_PRECISION = 'decimal:2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'free_chlorine',
        'ph',
        'alkalinity',
        'cyanuric_acid',
        'calcium_hardness',
        'salt',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'free_chlorine' => self::DECIMAL_PRECISION,
        'ph' => self::DECIMAL_PRECISION,
        'alkalinity' => self::DECIMAL_PRECISION,
        'cyanuric_acid' => self::DECIMAL_PRECISION,
        'calcium_hardness' => self::DECIMAL_PRECISION,
        'salt' => self::DECIMAL_PRECISION,
    ];

    /**
     * Get the business that owns the chemical record.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
