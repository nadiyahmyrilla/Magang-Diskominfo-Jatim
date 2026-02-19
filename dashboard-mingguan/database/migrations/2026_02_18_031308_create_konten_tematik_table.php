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
        Schema::create('konten_tematik', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_target');
            $table->string('agenda', 200);
            $table->decimal('progress', 5, 2);
            $table->string('data_dukung', 255)->nullable();
            $table->timestamps();
        });
    }
};
