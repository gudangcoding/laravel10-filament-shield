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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete()->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete()->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('satuan');
            $table->string('kategori')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('harga');
            $table->string('stok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};