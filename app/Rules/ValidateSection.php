<?php

namespace App\Rules;

use App\Models\Image;
use App\Models\Section;
use App\Models\Wysiwyg;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class ValidateSection implements ValidationRule
{
    /**
     * @param int $type Type of section: 1 for normal section and 2 for mini section
     */
    public function __construct(
        public int $type
    ) { }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!is_array($value)) {
            $fail(__('validation.array'));
            return;
        }

        $validator = Validator::make($value, [
            'name' => ['required', 'string', 'max:25'],
            'content' => ['nullable', 'array'],
            'id' => ['required', new IntegerOrString, new ModelExistsAndBelongsToAuthIfIdIsInteger(Section::class, ['type' => $this->type])],

            'content.minisections.*.content.sections' => ['prohibited'],
            'content.minisections.*.content.minisections' => ['prohibited'],

            'content.*' => ['nullable', 'array'], // array that contains sections, minisections, wysiwygs and images
            'content.*.*' => ['nullable', 'array', 'required_array_keys:position,id'], // individual sections, minisections, wysiwygs or images
            'content.*.*.position' => ['required', 'integer', 'distinct'],

            'content.sections.*' => [new ValidateSection(1)],
            // 'content.sections.*.id' => [], // already being validated with ValidateSection above
            
            'content.minisections.*' => [new ValidateSection(2)],
            // 'content.minisections.*.id' => [], // already being validated with ValidateSection above

            'content.wysiwygs.*.id' => ['required', new IntegerOrString, new ModelExistsAndBelongsToAuthIfIdIsInteger(Wysiwyg::class)],
            'content.wysiwygs.*.content' => ['string', 'max:14000'],

            'content.images.*.id' => ['required', new IntegerOrString, new ImageWasUploaded, new ModelExistsAndBelongsToAuthIfIdIsInteger(Image::class)],
        ]);

        if($validator->fails()) {
            $fail($validator->errors()->first());
        }
    }
}
