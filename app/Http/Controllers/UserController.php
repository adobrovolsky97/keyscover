<?php

namespace App\Http\Controllers;

use App\Enums\Role\Role;
use App\Http\Resources\User\UserResource;
use App\Services\User\Contracts\UserServiceInterface;
use Auth;
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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->userService->withCount(['orders'])->getAllPaginated([
                'except' => [Auth::id()],
                'role'   => Role::USER->value
            ])
        );
    }
}
