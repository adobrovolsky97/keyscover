<?php

namespace App\Http\Requests\Product;

use App\Models\Product\Product;
use App\Services\Product\ProductService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class MassActionRequest
 */
class MassActionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids'    => ['required', 'array', 'min:1', 'distinct'],
            'ids.*'  => ['required', 'integer', Rule::exists(Product::getTableName(), 'id')],
            'action' => [
                'required',
                'string',
                Rule::in([
                    ProductService::ACTION_DELETE,
                    ProductService::ACTION_HIDE,
                    ProductService::ACTION_SHOW
                ])
            ]
        ];
    }
}
