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
        Schema::create('portal_sata', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_target');
            $table->string('nama_dataset', 200);
            $table->string('dataset', 150);
            $table->string('target_total', 100);
            $table->string('capaian', 100);
            $table->decimal('capaian(%)', 5, 2);
            $table->timestamps();
        });
    }
};
