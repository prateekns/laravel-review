<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

/**
 * Page Model
 *
 * @property string|null $privacy_policy
 */
class Page extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'privacy_policy',
    ];
}
