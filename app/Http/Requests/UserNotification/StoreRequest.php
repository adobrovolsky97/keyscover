<?php

namespace App\Http\Requests\UserNotification;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreRequest
 */
class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['required', 'string']
        ];
    }
}
