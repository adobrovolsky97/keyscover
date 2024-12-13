<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SearchRequest
 */
class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_by' => ['nullable', 'string', Rule::in(['last_activity_at', 'created_at', 'orders_count'])],
            'order_dir'    => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
