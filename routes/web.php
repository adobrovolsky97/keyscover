<?php

use App\Enums\Product\Media;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{any}', function (Request $request) {

    $ogMeta = [];
    $defaultDescription = 'Оптовий продаж автомобільних ключів та все що з ними повязано. Співпраця виключно з майстрами!';
    $defaultTitle = 'KeysCover';

    if ($request->is('product/*')) {
        $productId = $request->segment(2);

        $product = Product::query()->whereKey($productId)->first();

        if (!empty($product)) {
            $image = $product->getFirstMediaUrl(Media::COLLECTION_IMAGES->value, 'watermarked');
            $ogMeta = [
                'title'       => $product->name,
                'description' => $product->description ?? $defaultDescription,
                'image'       => empty($image) ? asset('no-image.png') : $image,
                'url'         => url()->current(),
                'type'        => 'product',
                'site_name'   => 'KeysCover',
                'locale'      => 'ua_UA',
            ];
        }
    }

    return view('index', ['meta' => $ogMeta, 'title' => $defaultTitle, 'description' => $defaultDescription]);
})->where('any', '.*');
