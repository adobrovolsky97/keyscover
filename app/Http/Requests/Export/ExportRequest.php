<?php

namespace App\Http\Requests\Export;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ExportRequest
 */
class ExportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255']
        ];
    }
}
