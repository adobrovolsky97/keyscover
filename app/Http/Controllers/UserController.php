<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use Auth;

/**
 * Class UserController
 */
class UserController extends Controller
{
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
}
