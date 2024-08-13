<?php

namespace App\Http\Requests\Product;

use App\Rules\ValidateCategories;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SearchRequest
 */
class SearchRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'search'     => ['nullable', 'string'],
            'categories' => ['nullable', 'string', new ValidateCategories()],
            'per_page'   => ['nullable', 'integer', Rule::in([20, 50, 100])],
        ];
    }
}
