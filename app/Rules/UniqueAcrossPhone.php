<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueAcrossPhone implements ValidationRule
{
    protected $exclude;

    public function __construct($exclude = null)
    {
        $this->exclude = $exclude; // ID to exclude during update, if needed
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table('clients')
            ->where(function ($q) use ($value) {
                $q->where('phone1', $value)
                    ->orWhere('phone2', $value);
            });

        if ($this->exclude) {
            $query->where('id', '!=', $this->exclude);
        }

        if ($query->exists()) {
            $fail('The :attribute must be unique across all client phone numbers.');
        }
    }

    public function message()
    {
        return 'The :attribute must be unique across all client phone numbers.';
    }
}
