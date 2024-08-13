<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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
});

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
    });
});

/**
 * Users routes
 */
Route::get('users/me', [UserController::class, 'me']);

Route::get('cart', [CartController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);

Route::get('products', [ProductController::class, 'index']);
Route::get('configs', [ConfigController::class, 'index']);

