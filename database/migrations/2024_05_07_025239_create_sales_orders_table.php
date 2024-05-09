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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('data_alamat_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('satuan_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('harga', 8, 2)->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();
        });
        Schema::create('sales_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('satuan_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('harga', 8, 2)->nullable();
            $table->integer('qty')->nullable();
            $table->integer('subtotal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_detail');
        Schema::dropIfExists('sales_orders');
    }
};
