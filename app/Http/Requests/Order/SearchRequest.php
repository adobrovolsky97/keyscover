<?php

namespace App\Http\Requests\Order;

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
            'search' => ['nullable', 'string'],
            'sort_by'=> [
                'nullable',
                'string',
                Rule::in([
                    'created_at',
                    'total_price_uah',
                ])
            ],
            'order_dir' => ['nullable', 'string', Rule::in(['asc', 'desc'])]
        ];
    }
}
