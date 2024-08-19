<?php

namespace App\Models\Visit;

use App\Models\User\User;
use Carbon\Carbon;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Visit
 *
 * @property integer $id
 * @property string $ip
 * @property integer $user_id
 * @property string $user_agent
 * @property string $url
 * @property string $date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Visit extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = ['ip', 'user_id', 'user_agent', 'url', 'date', 'created_at', 'updated_at'];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
