<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SetNewPasswordRequest;
use App\Http\Requests\Auth\ValidateResetCodeRequest;
use App\Services\Auth\Contracts\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as Status;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
    /**
     * @var AuthServiceInterface
     */
    protected AuthServiceInterface $authService;

    /**
     * @param AuthServiceInterface $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Reset password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->generateResetCode($request->input('email'));

        return Response::json(null, Status::HTTP_NO_CONTENT);
    }

    /**
     * Validate reset code
     *
     * @param ValidateResetCodeRequest $request
     * @return JsonResponse
     */
    public function validateCode(ValidateResetCodeRequest $request): JsonResponse
    {
        if(!$this->authService->validateResetCode($request->input('email'), $request->input('code'))) {
            return Response::json(null, Status::HTTP_UNPROCESSABLE_ENTITY);
        }

        return Response::json(null, Status::HTTP_NO_CONTENT);
    }

    /**
     * Set new password
     *
     * @param SetNewPasswordRequest $request
     * @return JsonResponse
     */
    public function setNewPassword(SetNewPasswordRequest $request): JsonResponse
    {
        $this->authService->setNewPassword(
            $request->input('email'),
            $request->input('password'),
            $request->input('code')
        );

        return Response::json(null, Status::HTTP_NO_CONTENT);
    }

    /**
     * Register user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authService->register($request->validated());

        return Response::json(null, Status::HTTP_NO_CONTENT);
    }

    /**
     * Logout user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return Response::json(null, Status::HTTP_NO_CONTENT);
    }
}
