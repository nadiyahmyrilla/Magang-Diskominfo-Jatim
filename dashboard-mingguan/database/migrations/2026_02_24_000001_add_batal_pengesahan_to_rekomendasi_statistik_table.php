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
        Schema::table('rekomendasi_statistik', function (Blueprint $table) {
            $table->string('Batal', 100)->default('0')->after('Total');
            $table->string('Pengesahan', 100)->default('0')->after('Pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekomendasi_statistik', function (Blueprint $table) {
            $table->dropColumn(['Batal', 'Pengesahan']);
        });
    }
};
