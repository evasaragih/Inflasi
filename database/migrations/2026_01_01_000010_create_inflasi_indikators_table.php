<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inflasi_indikators', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah'); // nasional | sumbar | padang
            $table->string('bulan');
            $table->unsignedTinyInteger('urutan_bulan');
            $table->unsignedSmallInteger('tahun')->default(2026);
            $table->decimal('ihk', 8, 2)->nullable();
            $table->decimal('mtm', 8, 2)->nullable();
            $table->decimal('ytd', 8, 2)->nullable();
            $table->decimal('yoy', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inflasi_indikators');
    }
};
