<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_pangans', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun')->default(2026);
            $table->integer('urutan_bulan')->default(1);
            $table->string('bulan')->default('Januari');
            $table->string('komoditas');
            $table->double('stok')->default(0);
            $table->double('kebutuhan')->default(0);
            $table->string('satuan')->default('Ton');
            $table->double('harga_pasar')->nullable()->default(0);
            $table->double('het')->nullable()->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['tahun', 'urutan_bulan', 'komoditas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_pangans');
    }
};
