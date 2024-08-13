<?php

namespace App\Console\Commands;

use App\Enums\Config\Key;
use App\Enums\Config\Type;
use App\Services\Config\Contracts\ConfigServiceInterface;
use DOMDocument;
use DOMXPath;
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
        $html = Http::get('https://lion-kurs.rv.ua/')->body();

        $html = str_replace("\u{FEFF}", '', $html);
        $dom = new DOMDocument;
        libxml_use_internal_errors(true); // Suppress warnings due to malformed HTML
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $query = "//table/tr[td/img[contains(@alt, 'курс долара США')]]";
        $entries = $xpath->query($query);
        $dollarRates = [];
        foreach ($entries as $entry) {
            $buyingRate = $entry->getElementsByTagName('td')->item(0)->nodeValue;
            $sellingRate = $entry->getElementsByTagName('td')->item(2)->nodeValue;

            $dollarRates = [
                'buying'  => trim($buyingRate),
                'selling' => trim($sellingRate),
            ];
        }

        if (empty($dollarRates)) {
            $this->error('Failed to fetch dollar rate');
            return;
        }
        $this->info('USD rate: ' . $dollarRates['selling']);
        $configService->updateOrCreate(
            ['key' => Key::DOLLAR],
            ['value' => round($dollarRates['selling'], 2), 'type' => Type::FLOAT]
        );
    }
}
