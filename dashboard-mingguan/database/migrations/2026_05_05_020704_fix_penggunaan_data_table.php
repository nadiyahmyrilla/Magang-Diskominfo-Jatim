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
        Schema::table('penggunaan_data', function (Blueprint $table) {
            // Tambahkan kolom periode_awal jika belum ada
            if (!Schema::hasColumn('penggunaan_data', 'periode_awal')) {
                $table->date('periode_awal')->nullable()->after('id');
            }
            // Tambahkan kolom periode_akhir jika belum ada
            if (!Schema::hasColumn('penggunaan_data', 'periode_akhir')) {
                $table->date('periode_akhir')->nullable()->after('periode_awal');
            }
            // Tambahkan kolom tanggal jika belum ada
            if (!Schema::hasColumn('penggunaan_data', 'tanggal')) {
                $table->date('tanggal')->nullable()->after('periode_akhir');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggunaan_data', function (Blueprint $table) {
            // Drop kolom yang ditambahkan
            if (Schema::hasColumn('penggunaan_data', 'periode_awal')) {
                $table->dropColumn('periode_awal');
            }
            if (Schema::hasColumn('penggunaan_data', 'periode_akhir')) {
                $table->dropColumn('periode_akhir');
            }
            if (Schema::hasColumn('penggunaan_data', 'tanggal')) {
                $table->dropColumn('tanggal');
            }
        });
    }
};
