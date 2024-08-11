<?php

namespace App\Models\Config;

use App\Enums\Config\Key;
use App\Enums\Config\Type;
use Carbon\Carbon;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;

/**
 * Class Config
 *
 * @property integer $id
 * @property Key $key
 * @property string $value
 * @property Type $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Config extends BaseModel
{
	/**
	 * @var array
	 */
	protected $fillable = ['key', 'value', 'type', 'created_at', 'updated_at'];

    /**
     * @var string[]
     */
    protected $casts = [
        'key' => Key::class,
        'type' => Type::class
    ];
}
