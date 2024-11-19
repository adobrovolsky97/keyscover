<?php

namespace App\Http\Requests\Export;

use App\Models\Export\Export;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ExportRequest
 */
class ExportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in([Export::TYPE_USERS, Export::TYPE_ORDERS])],
            'params' => ['nullable', 'array']
        ];
    }
}
