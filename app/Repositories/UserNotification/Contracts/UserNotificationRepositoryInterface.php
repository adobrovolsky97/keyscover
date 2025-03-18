<?php

namespace App\Repositories\UserNotification\Contracts;

use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\Contracts\BaseRepositoryInterface;

/**
 * Interface UserNotificationRepositoryInterface
 */
interface UserNotificationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $userId
     * @return void
     */
    public function readAllForUser(int $userId): void;

    /**
     * @param string $batchUuid
     * @return void
     */
    public function deleteAllByBatchUuid(string $batchUuid): void;
}
