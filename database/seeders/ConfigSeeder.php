<?php

namespace Database\Seeders;

use App\Console\Commands\FetchDollarCommand;
use App\Enums\Config\Key;
use App\Enums\Config\Type;
use App\Models\Config\Config;
use Artisan;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::create([
           'key' => Key::FIVE_PERCENT_DISCOUNT_SUM,
           'value' => 200,
           'type' => Type::INTEGER,
        ]);

        Config::create([
           'key' => Key::TEN_PERCENT_DISCOUNT_SUM,
           'value' => 400,
           'type' => Type::INTEGER,
        ]);

        Artisan::call(FetchDollarCommand::class);
    }
}
