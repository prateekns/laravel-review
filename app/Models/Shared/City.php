<?php

namespace App\Models\Shared;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state_id',
    ];

    /**
     * Get the state that owns the city.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the country through the state relationship.
     */
    public function country(): BelongsTo
    {
        return $this->state->country();
    }

    /**
     * Get the businesses for the city.
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
