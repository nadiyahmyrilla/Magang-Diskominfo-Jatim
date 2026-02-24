<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DaftarData;
use Illuminate\Http\Request;

class DaftarDataController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = DaftarData::query();

        // Filter search (perangkat_daerah)
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('perangkat_daerah', 'like', '%' . $request->search . '%');
            });
        }

        // Filter tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $baseQuery->whereBetween('tanggal_target', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        $adaFilter = $request->filled('search') || ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir'));

        // Table data
        $tableQuery = clone $baseQuery;
        $data = $tableQuery->orderBy('tanggal_target', 'desc')->paginate(10)->withQueryString();

        // Stats & chart
        $statQuery = clone $baseQuery;
        if (!$adaFilter) {
            $tanggalTerbaru = DaftarData::max('tanggal_target');
            $statQuery->whereDate('tanggal_target', $tanggalTerbaru);
        }
        $dataStat = $statQuery->get();

        $totalJumlah         = $dataStat->sum('jumlah');
        $totalPerangkat      = $dataStat->count();
        $rataRataJumlah      = $totalPerangkat > 0 ? round($totalJumlah / $totalPerangkat, 2) : 0;
        $tanggalTerbaru      = $dataStat->max('tanggal_target');

        $chartLabels    = $dataStat->pluck('perangkat_daerah');
        $chartJumlah    = $dataStat->pluck('jumlah');

        return view('user.daftar_data.index', compact(
            'data',
            'totalJumlah',
            'totalPerangkat',
            'rataRataJumlah',
            'tanggalTerbaru',
            'chartLabels',
            'chartJumlah',
            'adaFilter'
        ));
    }
}
