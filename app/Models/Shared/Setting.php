<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\PriceException;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_price',
        'technician_price',
        'training_video',
        'trial_period',
        'trail_admin',
        'trail_technician',
        'admin_email',
        'website_url',
        'contact_phone',
        'jobs_synced_at',
        'discount_half_yearly',
        'discount_yearly'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'admin_price' => 'decimal:2',
        'technician_price' => 'decimal:2',
        'jobs_synced_at' => 'datetime',
    ];

    /**
     * Validation rules for the model.
     *
     * @var array<string, string>
     */
    public static $rules = [
        'admin_price' => 'required|numeric|gt:0',
        'technician_price' => 'required|numeric|gt:0',
    ];

    /**
     * Validate the model's attributes.
     *
     * @throws PriceException
     */
    public function validate(): bool
    {
        $validator = Validator::make($this->attributes, static::$rules);

        if ($validator->fails()) {
            throw new PriceException($validator->errors()->first());
        }

        return true;
    }
}
