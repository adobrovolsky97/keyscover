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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('surname');
            $table->string('name');
            $table->string('patronymic');
            $table->string('phone');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('delivery_type');
            $table->text('comment')->nullable();
            $table->string('city_id')->nullable();
            $table->string('city_name')->nullable();
            $table->string('warehouse_id')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->decimal('total_price_usd');
            $table->decimal('total_price_uah');
            $table->decimal('discount_percent')->default(0);
            $table->decimal('discount_usd')->default(0);
            $table->decimal('discount_uah')->default(0);
            $table->decimal('dollar_rate');
            $table->boolean('is_crm_synced')->default(false);
            $table->integer('crm_order_id')->nullable();
            $table->text('sync_error')->nullable();
            $table->timestamps();
        });

        Schema::create('order_products', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('total_price');
            $table->integer('total_price_uah');
            $table->primary(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('orders');
    }
};
