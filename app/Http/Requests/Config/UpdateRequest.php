<?php

namespace App\Http\Requests\Config;

use App\Models\Config\Config;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest
 */
class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var Config $config */
        $config = $this->route('config');

        $rules = $config->key->getValidationRules();

        return [
            'value' => $rules
        ];
    }
}
