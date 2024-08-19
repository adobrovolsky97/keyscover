<?php

namespace App\Providers;

use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\Contracts\CartRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\Contracts\CategoryRepositoryInterface;
use App\Repositories\Config\ConfigRepository;
use App\Repositories\Config\Contracts\ConfigRepositoryInterface;
use App\Repositories\Export\Contracts\ExportRepositoryInterface;
use App\Repositories\Export\ExportRepository;
use App\Repositories\Order\Contracts\OrderRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Product\Contracts\ProductRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\Visit\Contracts\VisitRepositoryInterface;
use App\Repositories\Visit\VisitRepository;
use App\Services\Auth\AuthService;
use App\Services\Auth\Contracts\AuthServiceInterface;
use App\Services\Cart\CartService;
use App\Services\Cart\Contracts\CartServiceInterface;
use App\Services\Category\CategoryService;
use App\Services\Category\Contracts\CategoryServiceInterface;
use App\Services\Config\ConfigService;
use App\Services\Config\Contracts\ConfigServiceInterface;
use App\Services\Crm\Contracts\CrmServiceInterface;
use App\Services\Crm\CrmService;
use App\Services\Delivery\Contracts\DeliveryServiceInterface;
use App\Services\Delivery\DeliveryService;
use App\Services\Delivery\Drivers\DeliveryDriverInterface;
use App\Services\Delivery\Drivers\NewPostDriver;
use App\Services\Export\Contracts\ExportServiceInterface;
use App\Services\Export\ExportService;
use App\Services\Order\Contracts\OrderServiceInterface;
use App\Services\Order\OrderService;
use App\Services\Product\Contracts\ProductServiceInterface;
use App\Services\Product\ProductService;
use App\Services\User\Contracts\UserServiceInterface;
use App\Services\User\UserService;
use App\Services\Visit\Contracts\VisitServiceInterface;
use App\Services\Visit\VisitService;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(UserServiceInterface::class, UserService::class);
        $this->app->singleton(CrmServiceInterface::class, CrmService::class);
        $this->app->singleton(CategoryServiceInterface::class, CategoryService::class);
        $this->app->singleton(ProductServiceInterface::class, ProductService::class);
        $this->app->singleton(ConfigServiceInterface::class, ConfigService::class);
        $this->app->singleton(CartServiceInterface::class, CartService::class);
        $this->app->singleton(OrderServiceInterface::class, OrderService::class);
        $this->app->singleton(DeliveryServiceInterface::class, DeliveryService::class);
        $this->app->singleton(DeliveryDriverInterface::class, NewPostDriver::class);
        $this->app->singleton(ExportServiceInterface::class, ExportService::class);
        $this->app->singleton(VisitServiceInterface::class, VisitService::class);

        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->singleton(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->singleton(ConfigRepositoryInterface::class, ConfigRepository::class);
        $this->app->singleton(CartRepositoryInterface::class, CartRepository::class);
        $this->app->singleton(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->singleton(ExportRepositoryInterface::class, ExportRepository::class);
        $this->app->singleton(VisitRepositoryInterface::class, VisitRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
