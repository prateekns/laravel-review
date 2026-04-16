<?php

namespace App\Models\Business\Technician;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianPasswordOtp extends Model
{
    use HasFactory;
    protected $fillable = [
        'staff_id',
        'otp',
        'is_verified',
        'expires_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if the OTP is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Get the technician associated with this OTP
     */
    public function technician()
    {
        return $this->belongsTo(Technician::class, 'staff_id', 'staff_id');
    }
}
