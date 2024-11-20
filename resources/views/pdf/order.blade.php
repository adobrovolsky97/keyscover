<style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
    }
</style>


<div>
    <h4 class="text-center text-lg font-bold">{{$order->created_at->format('d.m.Y')}} №{{ $order->number }}</h4>

    <div>
        @if($order->delivery_type === \App\Models\Order\Order::DELIVERY_TYPE_NEW_POST)
            <p><b>Місто:</b> {{$order->city_name}}</p>
            <p><b>Відділення:</b> {{$order->warehouse_name}}</p>
        @endif
        <p><b>Отримувач:</b> {{$order->full_name}}</p>
    </div>
    <table style="border: 1px solid black; width: 100%">
        <thead>
        <tr>
            <th>Фото</th>
            <th>Назва</th>
            <th>Арт.</th>
            <th>Кількість</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->products as $orderProduct)
            <tr>
                <td>
                    <div class="avatar">
                        <div class="w-24 rounded">
                            @php
                                $image = asset('no-image.png');
                                $firstMedia = $orderProduct->product->getFirstMedia(\App\Enums\Product\Media::COLLECTION_IMAGES->value);

                                if($firstMedia) {
                                    $image = $firstMedia->getPath('watermarked');
                                }

                                if (file_exists($image)) {
                                    $img = Image::make($image)->resize(100, 100, function ($constraint) {
                                        $constraint->aspectRatio();
                                    })->encode('data-url');
                                } else {
                                    $img = Image::make(public_path('no-image.png'))->resize(100, 100, function ($constraint) {
                                        $constraint->aspectRatio();
                                    })->encode('data-url');
                                }
                            @endphp
                            <img src="{{ $img }}" width="100">
                        </div>
                    </div>
                </td>
                <td>
                <span class="link cursor-pointer"
                      style="display: inline-block; max-width: 250px; white-space: break-spaces; overflow: hidden; text-overflow: ellipsis;">
                         {{$orderProduct->product->name}}
                </span>
                </td>
                <td class="text-gray-400">
                     <span class="link cursor-pointer"
                           style="display: inline-block; max-width: 200px; white-space: break-spaces; overflow: hidden; text-overflow: ellipsis;">
                        {{ $orderProduct->product->sku }}
                     </span>
                </td>
                <td>{{ $orderProduct->quantity }} шт.</td>
            </tr>
            @if(!$loop->last)
                <tr>
                    <td colspan="4">
                        <div style="border-bottom: 1px solid black; width: 100%;"></div>
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
