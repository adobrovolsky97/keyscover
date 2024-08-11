<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateQuantityResource
 */
class UpdateQuantityRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1']
        ];
    }
}
