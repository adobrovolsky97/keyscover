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
 * @property string $file_path
 * @property Status $status
 * @property string $error
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Export extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = ['status', 'error', 'name', 'file_path', 'created_at', 'updated_at'];

    /**
     * @var \class-string[]
     */
    protected $casts = [
        'status' => Status::class
    ];
}
