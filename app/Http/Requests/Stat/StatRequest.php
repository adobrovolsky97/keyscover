<?php

namespace App\Http\Requests\Stat;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StatRequest
 */
class StatRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
