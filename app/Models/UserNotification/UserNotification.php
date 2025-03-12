<?php

namespace App\Models\UserNotification;

use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserNotification
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $text
 * @property boolean $is_read
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property User $user
 */
class UserNotification extends BaseModel
{
    protected $fillable = [
        'user_id',
        'text',
        'is_read'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
