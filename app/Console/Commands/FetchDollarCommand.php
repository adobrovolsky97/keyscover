<?php

namespace App\Console\Commands;

use App\Enums\Config\Key;
use App\Enums\Config\Type;
use App\Services\Config\Contracts\ConfigServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchDollarCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dollar:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch dollar from PrivatBank';

    /**
     * Execute the console command.
     */
    public function handle(ConfigServiceInterface $configService): void
    {
        $data = Http::get('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5')->json();

        $usd = collect($data)->firstWhere('ccy', 'USD');

        if (!empty($usd)) {
            $this->info('USD rate: ' . $usd['sale']);
            $configService->updateOrCreate(
                ['key' => Key::DOLLAR],
                ['value' => round($usd['sale'], 2), 'type' => Type::FLOAT]
            );
        } else {
            $this->error('Failed to fetch dollar rate');
        }
    }
}
