<?php

namespace App\Http\Requests;

use App\Rules\ValidateSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WorkflowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    function prepareForValidation()
    {
        $validator = Validator::make($this->post(), [
            'main_sections' => 'nullable|array',
            'main_sections.*' => 'json',
            'deleted' => 'sometimes|required|array',
            'deleted.*' => 'nullable|string'
        ]);
        if(!$validator->passes()) return;

        $main_sections = [];
        foreach ($this->input('main_sections', []) as $section) {
            $decoded = json_decode($section, true);
            if($decoded) $main_sections[] = $decoded;
        }
        $this->merge(['main_sections' => $main_sections]);

        if($this->has('deleted')) {
            $deleted = [];
            foreach ($this->input('deleted') as $key => $value) {
                if(!is_null($value)) $deleted[$key] = explode(',', $value);
            }
            $this->merge(['deleted' => $deleted]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:25', 
                Rule::unique('sections')->where('user_id', $this->user()->id)->where('type', 0)->ignore($this->workflow->id ?? null)
            ],

            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:' . 2*1000], // 2mb

            'main_sections' => ['nullable', 'array'],
            'main_sections.*' => ['array', new ValidateSection(1)],
            'main_sections.*.*' => 'required', // to include all keys and not only position (because of the rule below)
            'main_sections.*.position' => ['integer', 'distinct'],

            'deleted' => 'sometimes|array',
            'deleted.*' => 'nullable|array',
            'deleted.sections.*' => ['integer', Rule::exists('sections', 'id')->where('user_id', $this->user()->id)],
            'deleted.wysiwygs.*' => ['integer', Rule::exists('wysiwygs', 'id')->where('user_id', $this->user()->id)],
            'deleted.images.*' => ['integer', Rule::exists('images', 'id')->where('user_id', $this->user()->id)],
        ];
    }
}
