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
     * Generate reset password code
     *
     * @param string $email
     * @return void
     */
    public function generateResetCode(string $email): void;

    /**
     * Validate reset password code
     *
     * @param string $email
     * @param int $code
     * @return bool
     */
    public function validateResetCode(string $email, int $code): bool;

    /**
     * Set new password
     *
     * @param string $email
     * @param string $password
     * @param int $code
     * @return void
     */
    public function setNewPassword(string $email, string $password, int $code): void;

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
