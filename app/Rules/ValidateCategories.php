<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidateCategories implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $categories = explode(',', $value);

        $validator = Validator::make(['categories' => $categories], [
            'categories.*' => ['required', Rule::exists('categories', 'slug')]
        ]);

        if ($validator->fails()) {
            $fail('The categories field is invalid.');
        }
    }
}
