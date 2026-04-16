<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $business_id
 * @property string $name
 * @property string $description
 */
class Templates extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'name',
        'description',
        'type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'type_label',
    ];

    /**
     * Get the business that owns the template.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the checklist items for the template.
     */
    public function checklistItems(): HasMany
    {
        return $this->hasMany(Checklist::class, 'template_id');
    }

    /**
     * Set the name attribute.
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = trim($value ?? '');
    }

    /**
     * Get the name attribute.
     *
     * @param string $value
     * @return string
     */
    public function getNameAttribute($value): string //NOSONAR
    {
        return trim($value ?? ''); //NOSONAR
    }

    /**
     * Set the description attribute.
     *
     * @param string $value
     * @return void
     */
    public function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = trim($value ?? '');
    }

    /**
     * Get the description attribute.
     *
     * @param string $value
     * @return string
     */
    public function getDescriptionAttribute($value): string //NOSONAR
    {
        return trim($value ?? ''); //NOSONAR
    }

    public function getTypeLabelAttribute(): string //NOSONAR
    {
        return $this->type === 'WO' ? __('business.templates.work_order') : __('business.templates.maintenance');
    }
}
