<?php

namespace App\Http\Requests\Order;

use App\Models\Order\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreRequest
 */
class StoreRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'surname'        => ['required', 'string', 'max:255'],
            'name'           => ['required', 'string', 'max:255'],
            'patronymic'     => ['required', 'string', 'max:255'],
            'comment'        => ['nullable', 'string', 'max:255'],
            'phone'          => ['required', 'regex:/^(\d{10}|\d{12})$/'],
            'payment_type'   => [
                'required',
                'string',
                'max:255',
                Rule::in([Order::PAYMENT_BY_REQUISITES, Order::PAYMENT_TYPE_CASH_ON_DELIVERY])
            ],
            'delivery_type'  => ['required', 'string', 'max:255', Rule::in(['new-post', 'self-pickup'])],
            'city_id'        => ['nullable', 'required_if:delivery_type,new-post', 'string', 'max:255'],
            'city_name'      => ['nullable', 'required_if:delivery_type,new-post', 'string', 'max:255'],
            'warehouse_id'   => ['nullable', 'required_if:delivery_type,new-post', 'string', 'max:255'],
            'warehouse_name' => ['nullable', 'required_if:delivery_type,new-post', 'string', 'max:255'],
        ];
    }
}
