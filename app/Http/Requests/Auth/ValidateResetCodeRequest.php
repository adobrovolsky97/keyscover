<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ValidateResetCodeRequest extends FormRequest
{
    /**
     * @return
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'code'  => ['required', 'integer', 'digits:6']
        ];
    }
}
