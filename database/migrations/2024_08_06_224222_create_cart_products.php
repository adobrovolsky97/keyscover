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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->decimal('total', 10)->default(0);
            $table->integer('total_uah')->default(0);
            $table->decimal('dollar_rate', 10, 2)->default(0);
            $table->decimal('discount_percent')->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->integer('discount_amount_uah')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_products');
        Schema::dropIfExists('carts');
    }
};
