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
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('email');
        $table->string('phone')->nullable();
        $table->text('shipping_address');
        $table->string('shipping_city');
        $table->string('shipping_postal_code');
        $table->string('shipping_country')->default('France');
        $table->string('delivery_method');
        $table->decimal('subtotal', 10, 2);
        $table->decimal('tax', 10, 2);
        $table->decimal('delivery_cost', 10, 2);
        $table->decimal('total', 10, 2);
        $table->string('status')->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
