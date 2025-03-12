<?php

namespace App\Services\UserNotification\Contracts;

use Adobrovolsky97\LaravelRepositoryServicePattern\Services\Contracts\BaseCrudServiceInterface;

/**
 * Interface UserNotificationServiceInterface
 */
interface UserNotificationServiceInterface extends BaseCrudServiceInterface
{
    /**
     * @return void
     */
    public function readAll(): void;
}
