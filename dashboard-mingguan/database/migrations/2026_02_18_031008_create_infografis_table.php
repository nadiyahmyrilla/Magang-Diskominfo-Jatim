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
        Schema::create('infografis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_target');
            $table->string('periode', 255);
            $table->string('sosial', 100);
            $table->string('ekonomi', 100);
            $table->string('pertanian', 100);
            $table->string('link_bukti', 255)->nullable();
            $table->timestamps();
        });
    }
};
