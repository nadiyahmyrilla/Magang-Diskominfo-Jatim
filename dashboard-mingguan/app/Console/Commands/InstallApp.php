<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use PDO;

class InstallApp extends Command
{
    protected $signature = 'app:install';
    protected $description = 'Auto create database and run migration';

    public function handle()
    {
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST');

        try {
            // Koneksi TANPA database
            $pdo = new PDO(
                "mysql:host=$dbHost",
                $dbUser,
                $dbPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Create database jika belum ada
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
            $this->info("Database '$dbName' siap.");

        } catch (\Exception $e) {
            $this->error("Gagal membuat database: " . $e->getMessage());
            return;
        }

        // Refresh koneksi
        Config::set('database.connections.mysql.database', $dbName);
        DB::purge('mysql');

        // Jalankan migration
        $this->call('migrate', ['--force' => true]);

        $this->info("Instalasi selesai ✔");
    }
}
