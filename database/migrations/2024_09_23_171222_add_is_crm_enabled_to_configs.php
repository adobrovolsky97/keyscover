<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Models\Config\Config::query()->updateOrCreate(
            ['key' => \App\Enums\Config\Key::IS_CRM_ENABLED->value],
            [
                'value' => 1,
                'type'  => \App\Enums\Config\Type::BOOLEAN->value,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Config\Config::query()->where('key', \App\Enums\Config\Key::IS_CRM_ENABLED->value)->delete();
    }
};
