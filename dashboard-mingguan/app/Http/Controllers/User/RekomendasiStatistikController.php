<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RekomendasiStatistik;
use Illuminate\Http\Request;

class RekomendasiStatistikController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = RekomendasiStatistik::query();

        // Filter search (match admin: instansi_tujuan, Layak, Pemeriksaan, Pengajuan, Perbaikan, Total)
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('instansi_tujuan', 'like', '%' . $request->search . '%')
                  ->orWhere('Layak', 'like', '%' . $request->search . '%')
                  ->orWhere('Pemeriksaan', 'like', '%' . $request->search . '%')
                  ->orWhere('Pengajuan', 'like', '%' . $request->search . '%')
                  ->orWhere('Perbaikan', 'like', '%' . $request->search . '%')
                  ->orWhere('Total', 'like', '%' . $request->search . '%');
            });
        }

        // Filter tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $baseQuery->whereBetween('tanggal_target', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        $tableQuery = clone $baseQuery;
        $data = $tableQuery->orderBy('tanggal_target', 'desc')->paginate(10)->withQueryString();

        $statQuery = clone $baseQuery;
        $dataStat = $statQuery->get();

        // Stat cards
        $jumlahHari = $dataStat->filter(function ($item) {
            return $item->tanggal_target && \Carbon\Carbon::parse($item->tanggal_target)->isToday();
        })->count();

        $jumlahMinggu = $dataStat->filter(function ($item) {
            return $item->tanggal_target && \Carbon\Carbon::parse($item->tanggal_target)->greaterThan(now()->subWeek());
        })->count();

        $jumlahBulan = $dataStat->filter(function ($item) {
            return $item->tanggal_target && \Carbon\Carbon::parse($item->tanggal_target)->greaterThan(now()->subMonth());
        })->count();

        $jumlahTahun = $dataStat->filter(function ($item) {
            return $item->tanggal_target && \Carbon\Carbon::parse($item->tanggal_target)->greaterThan(now()->subYear());
        })->count();

        $tanggalTerbaru = $dataStat->max('tanggal_target');

        $chartLabels = $dataStat->pluck('tanggal_target')->map(function($d) { return $d ? (is_string($d) ? (\Str::contains($d, 'T') ? \Str::before($d, 'T') : $d) : (method_exists($d, 'format') ? $d->format('Y-m-d') : $d)) : null; });
        $chartLayak = $dataStat->pluck('Layak');
        $chartPemeriksaan = $dataStat->pluck('Pemeriksaan');
        $chartPengajuan = $dataStat->pluck('Pengajuan');

        return view('user.rekomendasi_statistik.index', compact(
            'data', 'jumlahHari', 'jumlahMinggu', 'jumlahBulan', 'jumlahTahun', 'tanggalTerbaru', 'chartLabels', 'chartLayak', 'chartPemeriksaan', 'chartPengajuan'
        ));
    }
}
