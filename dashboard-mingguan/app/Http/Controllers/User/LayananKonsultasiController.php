<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LayananKonsultasi;
use Illuminate\Http\Request;

class LayananKonsultasiController extends Controller
{
    public function index(Request $request)
    {
        // Base query and filters (same as admin)
        $baseQuery = LayananKonsultasi::query();

        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('perangkat_daerah', 'like', '%' . $request->search . '%')
                  ->orWhere('tanggal_target', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $baseQuery->whereBetween('tanggal_target', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        $adaFilter = $request->filled('search') || ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir'));

        // Table data (paginated)
        $tableQuery = clone $baseQuery;
        $data = $tableQuery->orderBy('tanggal_target', 'desc')->paginate(10)->withQueryString();

        // Stats and chart data
        $statQuery = clone $baseQuery;
        if (!$adaFilter) {
            $tanggalTerbaru = LayananKonsultasi::max('tanggal_target');
            if ($tanggalTerbaru) {
                $statQuery->whereDate('tanggal_target', $tanggalTerbaru);
            }
        }

        $dataStat = $statQuery->get();

        $jumlahLakiLaki = $dataStat->sum('laki_laki');
        $jumlahPerempuan = $dataStat->sum('perempuan');
        $jumlahPerangkatDaerah = $dataStat->count();
        $jumlahTotal = $jumlahLakiLaki + $jumlahPerempuan;
        $tanggalTerbaru = $dataStat->max('tanggal_target');

        $chartLabels = $dataStat->pluck('tanggal_target')->map(fn($d) => $d?->format('Y-m-d'));
        $chartLakiLaki = $dataStat->pluck('laki_laki');
        $chartPerempuan = $dataStat->pluck('perempuan');

        return view('user.layanan_konsultasi.index', compact(
            'data',
            'adaFilter',
            'jumlahLakiLaki',
            'jumlahPerempuan',
            'jumlahPerangkatDaerah',
            'jumlahTotal',
            'tanggalTerbaru',
            'chartLabels',
            'chartLakiLaki',
            'chartPerempuan'
        ));
    }
}
