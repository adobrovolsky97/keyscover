<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('crm_status')->nullable();
            $table->string('ttn')->nullable();
            $table->string('nova_poshta_status')->nullable();
            $table->boolean('is_paid')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('crm_status');
            $table->dropColumn('nova_poshta_status');
            $table->dropColumn('is_paid');
            $table->dropColumn('ttn');
        });
    }
};
