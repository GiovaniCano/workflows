<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ImageWasUploaded implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $is_int = filter_var($value, FILTER_VALIDATE_INT);
        if(!$is_int && !isset(request()->file('images')[$value])) {
            $fail($attribute . ' image was not uploaded');
        }
    }
}
