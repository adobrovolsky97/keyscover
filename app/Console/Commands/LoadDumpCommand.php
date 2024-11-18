<?php

namespace App\Console\Commands;

use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Models\User\User;
use Illuminate\Console\Command;
use Storage;

class LoadDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load database dump from the files';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (!Storage::disk('public')->exists('backups')) {
            $this->error('No backups found');
            return;
        }

        $this->info('Loading users...');
        $this->loadUsers();
        $this->info('Users loaded');

        $this->info('Loading orders...');
        $this->loadOrders();
        $this->info('Orders loaded');

        $this->info('Loading products...');
        $this->loadProducts();
        $this->info('Products loaded');
    }

    protected function loadOrders(): void
    {
        $ordersJson = Storage::disk('public')->get('backups/orders.json');

        if (empty($ordersJson)) {
            $this->error('No orders found');
            return;
        }

        $ordersData = json_decode($ordersJson, true);

        foreach ($ordersData as $orderData) {
            $products = $orderData['products'] ?? [];
            unset($orderData['products']);
            unset($orderData['id']);


            $user = User::query()->where('email', $orderData['user_email'])->first();

            if (empty($user)) {
                continue;
            }

            unset($orderData['user_email']);
            $orderData['user_id'] = $user->id;

            $order = Order::create($orderData);

            foreach ($products as $product) {
                $productModel = Product::query()->where('sku', $product['sku'])->first();

                if (empty($productModel)) {
                    continue;
                }

                $order->products()->create([
                    'product_id'      => $productModel->id,
                    'quantity'        => $product['quantity'],
                    'total_price'     => $product['total_price'],
                    'total_price_uah' => $product['total_price_uah'],
                ]);

            }
        }
    }

    protected function loadProducts(): void
    {
        $productsJson = Storage::disk('public')->get('backups/products.json');

        if (empty($productsJson)) {
            $this->error('No products found');
            return;
        }

        $productsData = json_decode($productsJson, true);

        foreach ($productsData as $productData) {
            $this->info('Loading product ' . $productData['sku']);

            $productModel = Product::query()->where('sku', $productData['sku'])->first();

            if (empty($productModel) || empty($productData['media'])) {
                continue;
            }

            foreach ($productData['media'] as $media) {

                $fullPath = 'backups/product-images/' . $media;
                if (!Storage::disk('public')->exists($fullPath)) {
                    $this->error('Media file not found: ' . $media);
                    continue;
                }

                $this->info('Loading media ' . $media);
                $collection = \App\Enums\Product\Media::COLLECTION_IMAGES->value;
                $productModel->addMediaFromDisk($fullPath, 'public')->toMediaCollection($collection);
                Storage::disk('public')->delete($fullPath);
            }
        }
    }

    /**
     * @return void
     */
    protected function loadUsers(): void
    {
        $usersJson = Storage::disk('public')->get('backups/users.json');

        if (empty($usersJson)) {
            $this->error('No users found');
            return;
        }

        $usersData = json_decode($usersJson, true);

        foreach ($usersData as $userData) {
            unset($userData['id']);
            $this->info('Loading user ' . $userData['email']);
            User::query()->firstOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
