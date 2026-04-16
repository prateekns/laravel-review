<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    public const MONTHLY = 'monthly';

    public const YEARLY = 'yearly';

    public const DAILY = 'daily';

    public const HALF_YEARLY = 'half-yearly';

    protected $fillable = [
        'description',
        'price_id',
        'interval',
        'price',
        'status',
    ];
}
