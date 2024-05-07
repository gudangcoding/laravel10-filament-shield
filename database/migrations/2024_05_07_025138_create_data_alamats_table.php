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
            $table->string('no_hp');
            $table->text('alamat');
            $table->string('kelurahan');
            $table->string('kec');
            $table->string('kel');
            $table->string('kab');
            $table->string('prov');
            $table->string('kodepos');
            $table->string('tipe');
            $table->string('nama_bank');
            $table->string('no_rekening');
            $table->string('atas_nama');
            $table->string('nama_toko');
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