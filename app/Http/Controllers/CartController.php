<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddProductRequest;
use App\Http\Requests\Cart\UpdateQuantityRequest;
use App\Http\Resources\Cart\CartResource;
use App\Models\Product\Product;
use App\Services\Cart\Contracts\CartServiceInterface;
use Illuminate\Http\JsonResponse;
use Response as ResponseFacade;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CartController
 */
class CartController extends Controller
{
    /**
     * @var CartServiceInterface
     */
    private CartServiceInterface $cartService;

    /**
     * @param CartServiceInterface $cartService
     */
    public function __construct(CartServiceInterface $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @return CartResource
     */
    public function index(): CartResource
    {
        return CartResource::make($this->cartService->getUserCart());
    }

    /**
     * Add product to cart
     *
     * @param Product $product
     * @param AddProductRequest $request
     * @return CartResource
     */
    public function addProduct(Product $product, AddProductRequest $request): CartResource
    {
        return CartResource::make($this->cartService->addProductToCart($product, $request->input('quantity')));
    }

    /**
     * Update product quantity
     *
     * @param Product $product
     * @param UpdateQuantityRequest $request
     * @return CartResource
     */
    public function updateQuantity(Product $product, UpdateQuantityRequest $request): CartResource
    {
        return CartResource::make($this->cartService->updateQuantity($product, $request->input('quantity')));
    }

    /**
     * Delete product from cart
     *
     * @param Product $product
     * @return CartResource
     */
    public function deleteProduct(Product $product): CartResource
    {
        return CartResource::make($this->cartService->deleteProduct($product));
    }

    /**
     * @return JsonResponse
     */
    public function clearCart(): JsonResponse
    {
        $this->cartService->clearCart();

        return ResponseFacade::json(null, Response::HTTP_NO_CONTENT);
    }
}
