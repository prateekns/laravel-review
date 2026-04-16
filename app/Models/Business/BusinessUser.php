<?php

namespace App\Models\Business;

use App\Notifications\Business\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Helpers\Helper;

class BusinessUser extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const STATUS_ACTIVE = 1;

    public const STATUS_INACTIVE = 0;

    public const IS_PRIMARY = 1;

    public const NOT_PRIMARY = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'is_primary',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_primary' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Get the business that the user belongs to.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class)->withTrashed();
    }

    /**
     * Get the name of the user.
     */
    protected function adminName(): Attribute
    {
        return Attribute::get(
            fn () => "{$this->first_name} {$this->last_name}"
        );
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this->business->name));
    }

    /**
     * Scope a query to only include active users.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include non-primary users.
     */
    #[Scope]
    protected function notPrimary(Builder $query): void
    {
        $query->where('is_primary', self::NOT_PRIMARY);
    }

    /**
     * Get the formatted user created date.
     */
    public function getCreatedDateAttribute(): string
    {
        return Helper::getFormattedDate($this->created_at);
    }
}
