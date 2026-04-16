<?php

namespace App\Models\Shared;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    public const ACTIVE = 1;
    public const INACTIVE = 0;

    protected $fillable = [
        'name',
        'code',
        'phone_code',
    ];

    /**
     * Get the states for the country.
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    /**
     * Get the businesses for the country.
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
