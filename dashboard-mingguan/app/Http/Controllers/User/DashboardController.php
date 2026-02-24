<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Infografis;
use App\Models\KepuasanPengunjung;
use App\Models\KontenTematik;
use App\Models\LayananKonsultasi;
use App\Models\PenggunaanData;
use App\Models\PortalSata;
use App\Models\RekomendasiStatistik;
use App\Models\DaftarData;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get count data
        $infografisCount = Infografis::count();
        $kepuasanPengunjungCount = KepuasanPengunjung::count();
        $kontenTematikCount = KontenTematik::count();
        $layananKonsultasiCount = LayananKonsultasi::count();

        // Get agendas
        $kontenTematikAgendas = KontenTematik::whereMonth('tanggal_target', $currentMonth)
            ->whereYear('tanggal_target', $currentYear)
            ->orderBy('tanggal_target')
            ->get();

        // Get chart data
        $portalSataData = PortalSata::select('nama_dataset', 'capaian')
            ->where('tanggal_target', '>=', now()->startOfMonth())
            ->where('tanggal_target', '<=', now()->endOfMonth())
            ->orderBy('tanggal_target')
            ->get();

        $kepuasanData = KepuasanPengunjung::select('tanggal_target', 'sangat_puas', 'puas', 'tidak_puas', 'sangat_tidak_puas')
            ->whereMonth('tanggal_target', $currentMonth)
            ->whereYear('tanggal_target', $currentYear)
            ->orderBy('tanggal_target')
            ->get();

        // Return view dari admin folder (user hanya read-only)
        return view('admin.dashboard.index', compact(
            'infografisCount',
            'kepuasanPengunjungCount',
            'kontenTematikCount',
            'layananKonsultasiCount',
            'kontenTematikAgendas',
            'portalSataData',
            'kepuasanData',
            'currentMonth',
            'currentYear'
        ));
    }
}
