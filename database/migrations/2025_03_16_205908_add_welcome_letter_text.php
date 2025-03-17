<?php

use App\Enums\Config\Key;
use App\Enums\Config\Type;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Models\Config\Config::create([
            'key' => Key::WELCOME_MESSAGE,
            'type' => Type::STRING,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Config\Config::where('key', Key::WELCOME_MESSAGE)->delete();
    }
};
