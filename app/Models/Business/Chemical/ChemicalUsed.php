<?php

namespace App\Models\Business\Chemical;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChemicalUsed extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chemical_used';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'unit'
    ];

    /**
     * Get the chemical logs that use this chemical.
     */
    public function chemicalLogs(): HasMany
    {
        return $this->hasMany(ChemicalLog::class);
    }
}
