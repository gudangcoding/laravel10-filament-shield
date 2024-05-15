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
            $table->uuid();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('gambar')->nullable();
            $table->string('nama_produk_cina');
            $table->string('nama_produk');
            $table->string('format_satuan')->nullable();
            $table->string('slug')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('tag')->nullable();
            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('gambar')->nullable();
            $table->integer('harga')->nullable();
            $table->string('satuan')->nullable();
            $table->string('ukuran_kemasan')->nullable();
            $table->integer('isi')->nullable();
            $table->integer('stok');
            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price');
        Schema::dropIfExists('products');
    }
};
