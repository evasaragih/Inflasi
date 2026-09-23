<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_pangans', function (Blueprint $table) {
            $table->id();
            $table->string('bulan');
            $table->unsignedTinyInteger('urutan_bulan');
            $table->unsignedSmallInteger('tahun')->default(2026);
            $table->string('komoditas');
            $table->unsignedTinyInteger('minggu');
            $table->string('satuan')->nullable();
            $table->decimal('het', 10, 2)->nullable();
            $table->decimal('harga_ini', 10, 2)->nullable();
            $table->decimal('harga_lalu', 10, 2)->nullable();
            $table->decimal('perubahan', 10, 2)->nullable();
            $table->decimal('persen', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_pangans');
    }
};
