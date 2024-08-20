<?php

namespace App\Console\Commands;

use App\Models\Order\Order;
use App\Models\OrderProduct\OrderProduct;
use App\Models\Product\Product;
use App\Models\User\User;
use Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;
use Str;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Cleaning old backup...');
        Artisan::call('backup:clean');

        $this->info('Creating backup of products...');
        $this->createProductsDump();

        $this->info('Creating backup of users...');
        $this->createUsersDump();

        $this->info('Creating backup of orders...');
        $this->createOrdersDump();

        $this->info('Creating new backup...');
        Artisan::call('backup:run');

        Storage::disk('public')->deleteDirectory('backups');
    }

    /**
     * @return void
     */
    protected function createOrdersDump(): void
    {
        $ordersBackup = [];

        Order::with('products')->chunkById(100, function (Collection $orders) use (&$ordersBackup) {
            $orders->each(function (Order $order) use (&$ordersBackup) {
                $ordersBackup[$order->id] = $order->toArray();
                $ordersBackup[$order->id]['user_email'] = $order->user->email;
                $ordersBackup[$order->id]['products'] = $order->products->map(function (OrderProduct $orderProduct) {
                    return [
                        'sku'             => $orderProduct->product->sku,
                        'quantity'        => $orderProduct->quantity,
                        'total_price'     => $orderProduct->total_price,
                        'total_price_uah' => $orderProduct->total_price_uah,
                    ];
                })->toArray();
            });
        });

        $this->createJsonFileWithData('orders.json', array_values($ordersBackup));
    }

    /**
     * @return void
     */
    protected function createProductsDump(): void
    {
        $productsBackup = [];

        Product::with('media')->chunkById(100, function (Collection $products) use (&$productsBackup) {
            $products->each(function (Product $product) use (&$productsBackup) {
                $productsBackup[$product->sku] = [
                    'sku'   => $product->sku,
                    'media' => $product->media->map(function (Media $media) use ($product) {
                        $newName = $media->collection_name . '-' . Str::slug($product->sku) . "-$media->name.$media->extension";

                        Storage::disk('public')->copy($media->getPathRelativeToRoot(), "backups/product-images/$newName");

                        return $newName;
                    })
                ];
            });
        });

        $this->createJsonFileWithData('products.json', array_values($productsBackup));
    }

    /**
     * @return void
     */
    protected function createUsersDump(): void
    {
        $usersBackup = [];

        User::chunkById(100, function (Collection $users) use (&$usersBackup) {
            $users->each(function (User $user) use (&$usersBackup) {
                $usersBackup[$user->id] = $user->toArray();
                $usersBackup[$user->id]['password'] = $user->password;
            });
        });

        $this->createJsonFileWithData('users.json', array_values($usersBackup));
    }

    protected function createJsonFileWithData(string $fileName, array $data): void
    {
        if (!Storage::disk('public')->exists('backups')) {
            Storage::disk('public')->makeDirectory('backups');
        }

        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(Storage::disk('public')->path("backups/$fileName"), $json);
    }
}
