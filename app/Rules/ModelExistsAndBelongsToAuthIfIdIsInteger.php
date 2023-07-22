<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ModelExistsAndBelongsToAuthIfIdIsInteger implements ValidationRule
{
    /**
     * Validate that, if the id is an integer, the model exists and belongs to the authenticated user
     * @param string $modelClass Model's class like App\Models\Model
     * @param array $extraData Extra data used to validate the id
     */
    public function __construct(
        public string $modelClass,
        public array $extraData = []
    ) { }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $is_int = filter_var($value, FILTER_VALIDATE_INT);
        if($is_int) {
            $model_exists = $this->modelClass::where(['id' => $value, 'user_id' => auth()->id(), ...$this->extraData])->exists();
            if(!$model_exists) $fail(__('validation.exists'));
        }
    }
}
