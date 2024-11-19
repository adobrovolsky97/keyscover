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
                                    $imageData = base64_encode(file_get_contents($image));
                                    $mimeType = mime_content_type($image);
                                } else {
                                    $imageData = base64_encode(file_get_contents(public_path('no-image.png')));
                                    $mimeType = mime_content_type(public_path('no-image.png'));
                                }
                            @endphp
                            <img src="data:{{ $mimeType }};base64,{{ $imageData }}" width="120">
                        </div>
                    </div>
                </td>
                <td><span class="link cursor-pointer">{{$orderProduct->product->name}}</span></td>
                <td class="text-gray-400">{{ $orderProduct->product->sku }}</td>
                <td>{{ $orderProduct->quantity }}</td>
            </tr>

        @endforeach
        </tbody>
    </table>
</div>
