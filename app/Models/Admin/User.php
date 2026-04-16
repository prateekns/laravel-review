<?php

namespace App\Models\Admin;

use App\Helpers\FileHelper;
use App\Helpers\Helper;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const STATUS_ACTIVE = true;

    public const STATUS_INACTIVE = false;

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'is_primary',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token, $this->name));
    }

    public function getUserAvatarAttribute()
    {
        return FileHelper::getImageUrl($this->avatar);
    }

    public function getUserInitialsAttribute()
    {
        return Helper::getInitials($this->name);
    }

    #[Scope]
    protected function active(Builder $query)
    {
        return$query->where('status', self::STATUS_ACTIVE);
    }

    #[Scope]
    protected function notPrimary(Builder $query)
    {
        return $query->where('is_primary', false);
    }
}
