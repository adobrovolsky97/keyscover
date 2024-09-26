<?php

namespace App\Http\Requests\Product;

use App\Enums\Role\Role;
use App\Rules\ValidateCategories;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SearchRequest
 */
class SearchRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'exclude_hidden' => Auth::user()?->role !== Role::ADMIN
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'search'         => ['nullable', 'string'],
            'categories'     => ['nullable', 'string', new ValidateCategories()],
            'per_page'       => ['nullable', 'integer', Rule::in([20, 50, 100])],
            'sort_by' => [
                'nullable', 'string', Rule::in(['name', 'sku', 'last_sync_at', 'left_in_stock', 'is_stop_crm_update', 'is_hidden'])
            ],
            'order_dir' => [
                'nullable', 'string', Rule::in(['asc', 'desc'])
            ],
            'order_by'       => [
                'nullable',
                'string',
                Rule::in(['id_desc', 'name_asc', 'price_asc', 'price_desc', 'popularity_desc'])
            ],
            'only_available' => ['nullable', 'boolean'],
            'exclude_hidden'      => ['nullable', 'boolean']
        ];
    }
}
