<?php

namespace App\Services\Auth;

use App\Models\User\User;
use App\Services\Auth\Contracts\AuthServiceInterface;
use App\Services\User\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthService
 */
class AuthService implements AuthServiceInterface
{
    /**
     * Register user
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        return app(UserServiceInterface::class)->create($data);
    }

    /**
     * Login user
     *
     * @param array $data
     * @return User|null
     */
    public function login(array $data): ?User
    {
        if(Auth::attempt($data)) {
            return Auth::user();
        }

        return null;
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::user()->token()?->revoke();
    }
}
