<?php

namespace App\Models\Export;

use App\Enums\Export\Status;
use Carbon\Carbon;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;

/**
 * Class Export
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $file_path
 * @property Status $status
 * @property string $error
 * @property array $params
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Export extends BaseModel
{
    const TYPE_USERS = 'users';
    const TYPE_ORDERS = 'orders';


    /**
     * @var array
     */
    protected $fillable = ['status', 'type', 'params', 'error', 'name', 'file_path', 'created_at', 'updated_at'];

    /**
     * @var \class-string[]
     */
    protected $casts = [
        'status' => Status::class,
        'params' => 'array',
    ];
}
