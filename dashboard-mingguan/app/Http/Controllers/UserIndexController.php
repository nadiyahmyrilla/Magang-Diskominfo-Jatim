<?php

namespace App\Http\Controllers;

use App\Models\Infografis;
use App\Models\PenggunaanData;
use App\Models\PortalSata;
use App\Models\KepuasanPengunjung;
use App\Models\KontenTematik;
use App\Models\DaftarData;
use App\Models\RekomendasiStatistik;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserIndexController extends Controller
{
    /**
     * Tampilkan user dashboard (public - tanpa login)
     */
    public function index(Request $request)
    {
        $today = now();

        // parse optional date range from query
        $tanggalAwal = $request->query('tanggal_awal');
        $tanggalAkhir = $request->query('tanggal_akhir');
        if ($tanggalAwal) {
            try {
                $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalAwal)->startOfDay();
            } catch (\Exception $e) {
                $startDate = null;
            }
        } else {
            $startDate = null;
        }
        if ($tanggalAkhir) {
            try {
                $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalAkhir)->endOfDay();
            } catch (\Exception $e) {
                $endDate = null;
            }
        } else {
            $endDate = null;
        }
        if ($startDate && !$endDate) {
            $endDate = (clone $startDate)->endOfDay();
        }

        /*
        |--------------------------------------------------------------------------
        | STAT CARDS - SECTION ATAS
        |--------------------------------------------------------------------------
        */
        // Card 1: Jumlah Infografis (Sosial dan kepandulukan)
        $infografisData = Infografis::where('periode', 'like', '%Sosial%')
            ->orWhere('periode', 'like', '%kepandulukan%')
            ->get();
        $jumlahInfografis1 = $infografisData->count();

        // Card 2: Jumlah Viewer Data (latest or today)
        $latestPenggunaanData = PenggunaanData::latest('tanggal')->first();
        $penggunaanDataView = $latestPenggunaanData ? PenggunaanData::whereDate('tanggal', $latestPenggunaanData->tanggal)->sum('view') : 0;

        // Card 3: Jumlah Download Data (latest or today)
        $penggunaanDataDownload = $latestPenggunaanData ? PenggunaanData::whereDate('tanggal', $latestPenggunaanData->tanggal)->sum('download') : 0;

        // Card 4: Jumlah Infografis (Pertanian dan pertambangan)
        $infografisData2 = Infografis::where('periode', 'like', '%Pertanian%')
            ->orWhere('periode', 'like', '%pertambangan%')
            ->get();
        $jumlahInfografis2 = $infografisData2->count();

        /*
        |--------------------------------------------------------------------------
        | KONTEN TEMATIK - CALENDAR & SCHEDULES
        |--------------------------------------------------------------------------
        */
        $currentMonth = $today->month;
        $currentYear = $today->year;
        $kontenTematikQuery = KontenTematik::query();
        if ($startDate && $endDate) {
            $kontenTematikQuery->whereBetween('tanggal_target', [$startDate->toDateString(), $endDate->toDateString()]);
        } else {
            $kontenTematikQuery->whereMonth('tanggal_target', $currentMonth)
                ->whereYear('tanggal_target', $currentYear);
        }
        $kontenTematikAgendas = $kontenTematikQuery->orderBy('tanggal_target', 'asc')->get();

        /*
        |--------------------------------------------------------------------------
        | PORTAL SATA - CHART DATA
        |--------------------------------------------------------------------------
        */
        $portalSataQuery = PortalSata::query();
        if ($startDate && $endDate) {
            $portalSataQuery->whereBetween('tanggal_target', [$startDate->toDateString(), $endDate->toDateString()]);
        } else {
            $portalSataQuery->whereDate('tanggal_target', '>=', now()->subDays(7));
        }
        $portalSataData = $portalSataQuery->orderBy('tanggal_target', 'asc')->get();

        $portalSataLabels = $portalSataData->pluck('tanggal_target')->map(function ($d) {
            return $d ? $d->format('Y-m-d') : null;
        });
        
        $portalSataDataset = $portalSataData->pluck('dataset')->map(function ($v) {
            return is_numeric($v) ? (float)$v : floatval(preg_replace('/[^0-9.]/', '', $v));
        });
        
        $portalSataCapaian = $portalSataData->pluck('capaian')->map(function ($v) {
            return is_numeric($v) ? (float)$v : floatval(preg_replace('/[^0-9.]/', '', $v));
        });

        /*
        |--------------------------------------------------------------------------
        | KEPUASAN PENGUNJUNG - TIME SERIES (line chart)
        |--------------------------------------------------------------------------
        */
        $kepuasanQuery = KepuasanPengunjung::query();
        if ($startDate && $endDate) {
            $kepuasanQuery->whereBetween('tanggal_target', [$startDate->toDateString(), $endDate->toDateString()]);
        } else {
            $kepuasanQuery->whereDate('tanggal_target', '>=', now()->subDays(6));
        }
        $kepuasanSeriesData = $kepuasanQuery->orderBy('tanggal_target', 'asc')->get();

        $kepuasanLabelsSeries = $kepuasanSeriesData->pluck('tanggal_target')->map(function ($d) {
            if (!$d) return null;
            if ($d instanceof \Carbon\Carbon) return $d->format('Y-m-d');
            try {
                return \Carbon\Carbon::parse($d)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        });
        $kepuasanSangatPuas = $kepuasanSeriesData->map(function ($r) {
            return is_numeric($r->sangat_puas) ? (int)$r->sangat_puas : intval(preg_replace('/[^0-9]/', '', $r->sangat_puas));
        });
        $kepuasanPuas = $kepuasanSeriesData->map(function ($r) {
            return is_numeric($r->puas) ? (int)$r->puas : intval(preg_replace('/[^0-9]/', '', $r->puas));
        });
        $kepuasanTidakPuas = $kepuasanSeriesData->map(function ($r) {
            return is_numeric($r->tidak_puas) ? (int)$r->tidak_puas : intval(preg_replace('/[^0-9]/', '', $r->tidak_puas));
        });

        /*
        |--------------------------------------------------------------------------
        | DAFTAR DATA - TABLE DATA (LATEST)
        |--------------------------------------------------------------------------
        */
        if ($startDate && $endDate) {
            $daftarDataTerbaru = DaftarData::whereBetween('tanggal_target', [$startDate->toDateString(), $endDate->toDateString()])
                ->orderBy('jumlah', 'desc')
                ->limit(5)
                ->get();
            $tanggalDaftarData = $startDate;
        } else {
            $latestDaftarDataDate = DaftarData::latest('tanggal_target')->first();
            $daftarDataTerbaru = $latestDaftarDataDate 
                ? DaftarData::whereDate('tanggal_target', $latestDaftarDataDate->tanggal_target)
                    ->orderBy('jumlah', 'desc')
                    ->limit(5)
                    ->get()
                : collect([]);
            $tanggalDaftarData = $latestDaftarDataDate ? $latestDaftarDataDate->tanggal_target : null;
        }

        /*
        |--------------------------------------------------------------------------
        | REKOMENDASI STATISTIK - TABLE DATA (LATEST)
        |--------------------------------------------------------------------------
        */
        if ($startDate && $endDate) {
            $rekomendasiTerbaru = RekomendasiStatistik::whereBetween('tanggal_target', [$startDate->toDateString(), $endDate->toDateString()])
                ->orderBy('Total', 'desc')
                ->limit(5)
                ->get();
            $tanggalRekomendasi = $startDate;
        } else {
            $latestRekomendasiDate = RekomendasiStatistik::latest('tanggal_target')->first();
            $rekomendasiTerbaru = $latestRekomendasiDate
                ? RekomendasiStatistik::whereDate('tanggal_target', $latestRekomendasiDate->tanggal_target)
                    ->orderBy('Total', 'desc')
                    ->limit(5)
                    ->get()
                : collect([]);
            $tanggalRekomendasi = $latestRekomendasiDate ? $latestRekomendasiDate->tanggal_target : null;
        }

        $rangeStart = $startDate ? $startDate->toDateString() : null;
        $rangeEnd = $endDate ? $endDate->toDateString() : null;

        return view('user.index', compact(
            'jumlahInfografis1',
            'penggunaanDataView',
            'penggunaanDataDownload',
            'jumlahInfografis2',
            'kontenTematikAgendas',
            'today',
            'currentMonth',
            'currentYear',
            'portalSataLabels',
            'portalSataDataset',
            'portalSataCapaian',
            'kepuasanLabelsSeries',
            'kepuasanSangatPuas',
            'kepuasanPuas',
            'kepuasanTidakPuas',
            'daftarDataTerbaru',
            'tanggalDaftarData',
            'rekomendasiTerbaru',
            'tanggalRekomendasi'
        ))->with([
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
        ]);
    }
}
