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
        Schema::create('layanan_konsultasi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_target');
            $table->string('perangkat_daerah', 250);
            $table->string('laki_laki', 100);
            $table->string('perempuan', 100);
            $table->timestamps();
        });
    }
};
