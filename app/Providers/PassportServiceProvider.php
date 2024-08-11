<?php

namespace App\Providers;

use App\GrantTypes\SocialGrantType;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

/**
 * Class PassportServiceProvider
 */
class PassportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->resolving(AuthorizationServer::class, function (AuthorizationServer $server) {
            $server->enableGrantType($this->makeSocialGrant(), Passport::tokensExpireIn());
        });
    }

    /**
     * @return SocialGrantType
     * @throws BindingResolutionException
     */
    protected function makeSocialGrant(): SocialGrantType
    {
        $grant = new SocialGrantType($this->app->make(RefreshTokenRepository::class));

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
