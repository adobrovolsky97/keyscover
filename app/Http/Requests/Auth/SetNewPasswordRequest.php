<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SetNewPasswordRequest
 */
class SetNewPasswordRequest extends FormRequest
{
    /**
     * @return
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'code'     => ['required', 'integer', 'digits:6']
        ];
    }
}
