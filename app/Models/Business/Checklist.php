<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklist_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'business_id',
        'template_id',
        'item_text',
        'is_visible',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the business that owns the checklist item.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the template that owns the checklist item.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Templates::class);
    }

    /**
     * Set the item text attribute.
     *
     * @param string $value
     * @return void
     */
    public function setItemTextAttribute($value): void
    {
        $this->attributes['item_text'] = trim($value ?? '');
    }

    /**
     * Get the item text attribute.
     *
     * @param string $value
     * @return string
     */
    public function getItemTextAttribute($value): string
    {
        return trim($value ?? '');
    }
}
