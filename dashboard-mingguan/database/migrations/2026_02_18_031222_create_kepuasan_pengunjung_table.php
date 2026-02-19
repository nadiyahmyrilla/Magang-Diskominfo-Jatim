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
        Schema::create('kepuasan_pengunjung', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_target');
            $table->string('jenis_kelamin', 150);
            $table->string('sangat_puas', 100);
            $table->string('puas', 100);
            $table->string('tidak_puas', 100);
            $table->string('sangat_tidak_puas', 100);
            $table->timestamps();
        });
    }
};
