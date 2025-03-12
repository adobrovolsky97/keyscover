<?php

namespace App\Http\Resources\UserNotification;

use App\Models\UserNotification\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserNotificationResource
 *
 * @mixin UserNotification
 */
class UserNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'text'       => $this->text,
            'is_read'    => $this->is_read,
            'created_at' => $this->created_at->format('d.m.Y H:i')
        ];
    }
}
