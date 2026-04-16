<?php

namespace App\Models\Business\Chemical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chemical extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'chemicals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'range',
        'ideal_target',
        'unit',
        'type'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'ideal_target' => 'decimal:2',
        'type' => 'integer',
    ];

    /**
     * Get the chemical logs for this chemical.
     */
    public function chemicalLogs(): HasMany
    {
        return $this->hasMany(ChemicalLog::class);
    }

    /**
     * Get the business chemicals for this chemical.
     */
    public function businessChemicals(): HasMany
    {
        return $this->hasMany(BusinessChemical::class);
    }

    /**
     * Check if this is a chemical (type = 1).
     */
    public function isChemical(): bool
    {
        return $this->type === 1;
    }

    /**
     * Check if this is an additional item (type = 2).
     */
    public function isAdditionalItem(): bool
    {
        return $this->type === 2;
    }

    /**
     * Get the type label attribute.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->isChemical() ? 'Chemical' : 'Additional Item';
    }
}
