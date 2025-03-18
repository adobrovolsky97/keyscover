<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSubscriptionController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\VisitTrackerController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * Guest only routes
 */
Route::group(['middleware' => 'guest:api'], function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('auth/validate-code', [AuthController::class, 'validateCode']);
    Route::post('auth/set-new-password', [AuthController::class, 'setNewPassword']);
});

/**
 * Visit tracker
 */
Route::post('track', [VisitTrackerController::class, 'track']);

/**
 * Auth protected routes
 */
Route::group(['middleware' => ['auth:api']], function () {

    /**
     * Auth routes
     */
    Route::delete('auth/logout', [AuthController::class, 'logout']);

    /**
     * Delivery
     */
    Route::group(['prefix' => 'delivery/{driver}'], function () {
        Route::get('cities', [DeliveryController::class, 'cities']);
        Route::get('cities/{cityId}/warehouses', [DeliveryController::class, 'warehouses']);
        Route::get('cities/{cityId}/addresses', [DeliveryController::class, 'addresses']);
    });

    /**
     * Cart
     */
    Route::group(['prefix' => 'cart'], function () {
        Route::post('{product}', [CartController::class, 'addProduct']);
        Route::patch('{product}', [CartController::class, 'updateQuantity']);
        Route::delete('{product}', [CartController::class, 'deleteProduct']);
        Route::delete('', [CartController::class, 'clearCart']);
    });

    /**
     * Ordering
     */
    Route::group(['prefix' => 'orders'], function () {
        Route::get('', [OrderController::class, 'index']);
        Route::post('', [OrderController::class, 'store']);
        Route::post('{order}/sync', [OrderController::class, 'syncOrderToCrm']);
    });

    Route::post('products/{product}/subscribe', [ProductSubscriptionController::class, 'store']);
    Route::post('favorites/{product}/toggle', [FavoriteController::class, 'toggle']);
    Route::get('favorites', [FavoriteController::class, 'index']);

    Route::get('notifications', [UserNotificationController::class, 'index']);
    Route::delete('notifications/{userNotification}', [UserNotificationController::class, 'destroy']);
    Route::get('notifications/count', [UserNotificationController::class, 'getCount']);
    Route::post('notifications/read-all', [UserNotificationController::class, 'readAllNotifications']);
});

/**
 * Users routes
 */
Route::get('users/me', [UserController::class, 'me']);

Route::get('cart', [CartController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);

Route::group(['prefix' => 'products'], function () {
    Route::get('', [ProductController::class, 'index']);
    Route::get('{product}', [ProductController::class, 'show']);
});

Route::get('configs', [ConfigController::class, 'index']);

/**
 * Admin routes
 */
Route::middleware(AdminMiddleware::class)->group(function () {
    /**
     * Users
     */
    Route::group(['prefix' => 'users'], function () {
        Route::get('', [UserController::class, 'index']);
        Route::delete('{user}', [UserController::class, 'destroy']);
    });

    Route::post('notifications', [UserNotificationController::class, 'store']);

    /**
     * Configs
     */
    Route::group(['prefix' => 'configs'], function () {
        Route::get('list', [ConfigController::class, 'list']);
        Route::get('{config}', [ConfigController::class, 'show']);
        Route::put('{config}', [ConfigController::class, 'update']);
    });

    /**
     * Exports
     */
    Route::get('export', [ExportController::class, 'export']);
    Route::group(['prefix' => 'exports'], function () {
        Route::get('', [ExportController::class, 'index']);
        Route::get('{export}/download', [ExportController::class, 'download']);
    });

    /**
     * Stats
     */
    Route::group(['prefix' => 'stats'], function () {
        Route::get('unique-visits', [StatController::class, 'uniqueVisits']);
        Route::get('non-unique-visits', [StatController::class, 'nonUniqueVisits']);
        Route::get('platforms', [StatController::class, 'platforms']);
        Route::get('browsers', [StatController::class, 'browsers']);
        Route::get('devices', [StatController::class, 'devices']);
    });

    Route::post('products/mass-actions', [ProductController::class, 'handleMassActions']);
    Route::post('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
});
