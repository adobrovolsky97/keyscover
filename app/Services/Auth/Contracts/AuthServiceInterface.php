<?php

namespace App\Services\Auth\Contracts;

use App\Models\User\User;

interface AuthServiceInterface
{
    /**
     * Register user
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User;

    /**
     * Login user
     *
     * @param array $data
     * @return User|null
     */
    public function login(array $data): ?User;

    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void;
}
