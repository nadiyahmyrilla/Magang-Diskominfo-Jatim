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
        Schema::create('rekomendasi_statistik', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_target');
            $table->string('instansi_tujuan', 150);
            $table->string('Layak', 100);
            $table->string('Pemeriksaan', 100);
            $table->string('Pengajuan', 100);
            $table->string('Perbaikan', 100);
            $table->string('Total', 100);
            $table->timestamps();
        });
    }
};
