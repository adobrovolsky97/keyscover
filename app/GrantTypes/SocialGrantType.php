<?php

namespace App\GrantTypes;

use App\Models\User\User as UserModel;
use App\Services\User\Contracts\UserServiceInterface;
use DateInterval;
use Exception;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Bridge\User;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\RequestAccessTokenEvent;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\RequestRefreshTokenEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class SocialGrantType
 */
class SocialGrantType extends AbstractGrant
{
    /**
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(RefreshTokenRepositoryInterface $refreshTokenRepository)
    {
        $this->setRefreshTokenRepository($refreshTokenRepository);

        $this->refreshTokenTTL = new DateInterval('P1M');
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'social';
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseTypeInterface $responseType
     * @param DateInterval $accessTokenTTL
     * @return ResponseTypeInterface
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType, DateInterval $accessTokenTTL): ResponseTypeInterface
    {
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request, $this->defaultScope));
        $user = $this->validateUser($request);

        // Finalize the requested scopes
        $finalizedScopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());

        // Issue and persist new access token
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $finalizedScopes);
        $this->getEmitter()->emit(new RequestAccessTokenEvent(RequestEvent::ACCESS_TOKEN_ISSUED, $request, $accessToken));
        $responseType->setAccessToken($accessToken);

        // Issue and persist new refresh token if given
        $refreshToken = $this->issueRefreshToken($accessToken);

        if ($refreshToken !== null) {
            $this->getEmitter()->emit(new RequestRefreshTokenEvent(RequestEvent::REFRESH_TOKEN_ISSUED, $request, $refreshToken));
            $responseType->setRefreshToken($refreshToken);
        }

        return $responseType;
    }

    /**
     * @param ServerRequestInterface $request
     * @return UserEntityInterface
     * @throws OAuthServerException
     * @throws Exception
     */
    protected function validateUser(ServerRequestInterface $request): UserEntityInterface
    {
        $provider = $this->getRequestParameter('provider', $request);

        if (!is_string($provider)) {
            throw OAuthServerException::invalidRequest('provider');
        }

        $socialUser = Socialite::driver($provider)->stateless()->userFromToken($this->getRequestParameter('access_token', $request));

        $userService = app(UserServiceInterface::class);

        /** @var UserModel $user */
        $user = $userService->find(['email' => $socialUser->email])->first();

        $user?->update(['provider' => $provider, 'provider_id' => $socialUser->id]);

        if ($user === null) {
            $user = $userService->create([
                'password'          => Hash::make(fake()->password),
                'provider'          => $provider,
                'provider_id'       => $socialUser->id,
                'email'             => $socialUser->email,
                'name'              => $socialUser->name,
                'email_verified_at' => now()
            ]);
        }

        return new User($user->getKey());
    }
}
