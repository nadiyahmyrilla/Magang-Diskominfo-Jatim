<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Infografis;
use Illuminate\Http\Request;

class InfografisController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Infografis::query();

        // Filter search
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('periode', 'like', '%' . $request->search . '%')
                  ->orWhere('tanggal_target', 'like', '%' . $request->search . '%');
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
            $tanggalTerbaru = Infografis::max('tanggal_target');
            $statQuery->whereDate('tanggal_target', $tanggalTerbaru);
        }
        $dataStat = $statQuery->get();

        $jumlahSosial    = $dataStat->sum('sosial');
        $jumlahEkonomi   = $dataStat->sum('ekonomi');
        $jumlahPertanian = $dataStat->sum('pertanian');
        $totalPeriode    = $dataStat->count();
        $tanggalTerbaru  = $dataStat->max('tanggal_target');

        $chartLabels     = $dataStat->pluck('tanggal_target');
        $chartSosial     = $dataStat->pluck('sosial');
        $chartEkonomi    = $dataStat->pluck('ekonomi');
        $chartPertanian  = $dataStat->pluck('pertanian');

        return view('user.infografis.index', compact(
            'data',
            'jumlahSosial',
            'jumlahEkonomi',
            'jumlahPertanian',
            'totalPeriode',
            'tanggalTerbaru',
            'chartLabels',
            'chartSosial',
            'chartEkonomi',
            'chartPertanian',
            'adaFilter'
        ));
    }
}
