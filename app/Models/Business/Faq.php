<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Faq
 *
 * @package App\Models\Business
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property string|null $category
 * @property bool $is_active
 * @property int $order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static Builder|self active()
 * @method static Builder|self byCategory(string $category)
 * @method static Builder|self ordered()
 */
class Faq extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faqs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'question',
        'answer',
        'link',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'question' => 'array',
        'answer' => 'array',
    ];


    /**
     * Get the translated question.
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getTranslatedQuestion($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->question[$locale] ?? $this->question['en'] ?? null;
    }

    /**
     * Get the translated answer.
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getTranslatedAnswer($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->answer[$locale] ?? $this->answer['en'] ?? null;
    }
}
