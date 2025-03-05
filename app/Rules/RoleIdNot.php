<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RoleIdNot implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    protected $exceptId;

    public function __construct($exceptId)
    {
        $this->exceptId = $exceptId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = \App\Models\User::find($value);
        if ($user && $user->role_id == $this->exceptId){
            $fail("$attribute is invalid because is admin.");
        }
    }
}
