<?php

namespace App\Services\Auth;

use App\Mail\SendResetCodeMail;
use App\Models\User\User;
use App\Services\Auth\Contracts\AuthServiceInterface;
use App\Services\User\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use Mail;

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
     * Generate reset password code
     *
     * @param string $email
     * @return void
     */
    public function generateResetCode(string $email): void
    {
        /** @var User $user */
        $user = app(UserServiceInterface::class)->find(['email' => $email])->firstOrFail();

        $user->resetPasswordCodes()->delete();

        $code = $this->generateCode();

        $user->resetPasswordCodes()->create([
            'code'       => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($email)->send(new SendResetCodeMail($code));
    }


    /**
     * Validate reset password code
     *
     * @param string $email
     * @param int $code
     * @return bool
     */
    public function validateResetCode(string $email, int $code): bool
    {
        /** @var User $user */
        $user = app(UserServiceInterface::class)->find(['email' => $email])->firstOrFail();

        return $user->resetPasswordCodes()
            ->where('code', $code)
            ->where('expires_at', '>=', now())
            ->exists();
    }


    /**
     * Set new password
     *
     * @param string $email
     * @param string $password
     * @param int $code
     * @return void
     */
    public function setNewPassword(string $email, string $password, int $code): void
    {
        /** @var User $user */
        $user = app(UserServiceInterface::class)->find(['email' => $email])->firstOrFail();

        // Validate code
        $user->resetPasswordCodes()
            ->where('code', $code)
            ->where('expires_at', '>=', now())
            ->firstOrFail();

        $user->resetPasswordCodes()->delete();

        $user->update([
            'password' => bcrypt($password),
        ]);
    }

    /**
     * Login user
     *
     * @param array $data
     * @return User|null
     */
    public function login(array $data): ?User
    {
        if (Auth::attempt($data)) {
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

    /**
     * @return int
     */
    protected function generateCode(): int
    {
        return rand(100000, 999999);
    }
}
