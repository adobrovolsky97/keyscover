<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Order\OrderResource;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'role'             => $this->role,
            'orders_count'     => $this->whenCounted('orders'),
            'last_activity_at' => $this->last_activity_at,
            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
            'last_order'       => OrderResource::make($this->orders()->latest('id')->first())
        ];
    }
}
