<?php

namespace App\Services\UserNotification;

use Adobrovolsky97\LaravelRepositoryServicePattern\Exceptions\Repository\RepositoryException;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;
use App\Models\User\User;
use App\Models\UserNotification\UserNotification;
use App\Services\UserNotification\Contracts\UserNotificationServiceInterface;
use App\Repositories\UserNotification\Contracts\UserNotificationRepositoryInterface;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Class UserNotificationService
 *
 * @property UserNotificationRepositoryInterface $repository
 */
class UserNotificationService extends BaseCrudService implements UserNotificationServiceInterface
{
    /**
     * @param array $data
     * @return Model|null
     */
    public function create(array $data): ?Model
    {
        return DB::transaction(function () use ($data) {
            $batchUuid = Uuid::uuid6()->toString();
            User::query()
                ->when(!empty($data['user_ids']), function (Builder $query) use ($data) {
                    $query->whereIn('id', $data['user_ids']);
                })
                ->chunk(200, function (Collection $users) use ($data, $batchUuid) {
                    $rows = [];
                    foreach ($users as $user) {
                        $rows[] = [
                            'user_id'               => $user->id,
                            'text'                  => $data['text'],
                            'is_admin_notification' => $data['is_admin_notification'] ?? false,
                            'batch_uuid'            => $batchUuid,
                            'created_at'            => now(),
                            'updated_at'            => now(),
                        ];
                    }

                    $this->repository->insert($rows);
                });

            return null;
        });
    }

    /**
     * @return void
     */
    public function readAll(): void
    {
        if (auth()->guest()) {
            abort(404);
        }

        $this->repository->readAllForUser(auth()->id());
    }

    /**
     * @param $keyOrModel
     * @return bool
     * @throws RepositoryException
     */
    public function delete($keyOrModel): bool
    {
        /** @var UserNotification $notification */
        $notification = $this->repository->resolveModel($keyOrModel);

        if (empty($notification->batch_uuid)) {
            abort(404);
        }


        $this->repository->deleteAllByBatchUuid($notification->batch_uuid);

        return true;
    }

    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return UserNotificationRepositoryInterface::class;
    }
}
