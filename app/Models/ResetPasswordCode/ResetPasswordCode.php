<?php

namespace App\Models\ResetPasswordCode;

use Carbon\Carbon;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;

/**
 * Class ResetPasswordCode
 * 
 * @property integer $id
 * @property integer $user_id
 * @property string $code
 * @property Carbon $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 */
class ResetPasswordCode extends BaseModel
{
	/**
	 * @var array
	 */
	protected $fillable = ['user_id', 'code', 'expires_at', 'created_at', 'updated_at'];

	/**
	 * @return BelongsTo
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}