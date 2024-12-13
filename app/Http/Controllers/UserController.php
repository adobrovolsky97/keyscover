<?php

namespace App\Http\Controllers;

use App\Enums\Role\Role;
use App\Http\Requests\User\SearchRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User\User;
use App\Services\User\Contracts\UserServiceInterface;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class UserController
 */
class UserController extends Controller
{
    private UserServiceInterface $userService;

    /**
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get user data
     *
     * @return UserResource|null
     */
    public function me(): ?UserResource
    {
        if (Auth::guest()) {
            return null;
        }

        return UserResource::make(Auth::guard('api')->user());
    }

    /**
     * Get users list
     *
     * @param SearchRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(SearchRequest $request): AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->userService->withCount(['orders'])->getAllPaginated(array_merge(
                [
                    'except' => [Auth::id()],
                    'role'   => Role::USER->value
                ],
                $request->validated()
            ), 50)
        );
    }

    /**
     * Delete user
     *
     * @param User $user
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return response()->json(null, 204);
    }
}
