<?php

namespace App\Http\Requests\Config;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest
 */
class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        // now I only support bool val
        return [
            'value' => ['required', 'boolean']
        ];
    }
}
