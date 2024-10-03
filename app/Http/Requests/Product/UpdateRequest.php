<?php

namespace App\Http\Requests\Product;

use App\Models\Product\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRequest
 */
class UpdateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'is_hidden'          => ['nullable', 'boolean'],
            'is_stop_crm_update' => ['nullable', 'boolean'],
            'main_image_index'   => ['nullable', 'integer'],
            'related_products'   => ['nullable', 'array', 'distinct'],
            'related_products.*' => ['required', 'integer', Rule::exists('products', 'id')->whereNot('id', $this->route('product')->id)],
            'similar_products'   => ['nullable', 'array', 'distinct'],
            'similar_products.*' => ['required', 'integer', Rule::exists('products', 'id')->whereNot('id', $this->route('product')->id)],
            'images'             => ['nullable', 'array'],
            'images.*'           => ['required', 'image'],
            'images_to_remove'   => ['nullable', 'array'],
            'images_to_remove.*' => [
                'required',
                'integer',
                Rule::exists('media', 'id')->where('model_type', Product::class)->where('model_id', $this->route('product')->id)
            ],
        ];
    }
}
