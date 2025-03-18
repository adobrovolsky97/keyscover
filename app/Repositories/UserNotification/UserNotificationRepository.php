<?php

namespace App\Repositories\UserNotification;

use App\Models\UserNotification\UserNotification;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;
use App\Repositories\UserNotification\Contracts\UserNotificationRepositoryInterface;

/**
 * Class UserNotificationRepository
 */
class UserNotificationRepository extends BaseRepository implements UserNotificationRepositoryInterface
{
    /**
     * @param int $userId
     * @return void
     */
    public function readAllForUser(int $userId): void
    {
        $this->getQuery()->where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);
    }

    /**
     * @param string $batchUuid
     * @return void
     */
    public function deleteAllByBatchUuid(string $batchUuid): void
    {
        $this->getQuery()->where('batch_uuid', $batchUuid)->delete();
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return UserNotification::class;
    }
}
