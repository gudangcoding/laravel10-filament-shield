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
        Schema::create('data_alamats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->nullable(true);
            $table->foreignId('team_id')->constrained()->cascadeOnDelete()->nullable(true);
            $table->string('nama');
            $table->string('type');
            $table->string('no_hp');
            $table->text('alamat');
            $table->string('kelurahan')->nullable();
            $table->string('kec')->nullable();
            $table->string('kel')->nullable();
            $table->string('kab')->nullable();
            $table->string('prov')->nullable();
            $table->string('kodepos')->nullable();
            $table->string('tipe')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('atas_nama')->nullable();
            $table->string('nama_toko')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_alamats');
    }
};
