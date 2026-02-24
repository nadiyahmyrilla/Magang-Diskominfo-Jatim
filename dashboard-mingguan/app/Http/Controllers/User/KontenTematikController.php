<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KontenTematik;
use Illuminate\Http\Request;

class KontenTematikController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = KontenTematik::query();

        // Filter search
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('agenda', 'like', '%' . $request->search . '%')
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
            $tanggalTerbaru = KontenTematik::max('tanggal_target');
            $statQuery->whereDate('tanggal_target', $tanggalTerbaru);
        }
        $dataStat = $statQuery->get();

        $jumlahAgenda    = $dataStat->count();
        $rataProgress    = $dataStat->count() > 0 ? $dataStat->avg('progress') : 0;
        $totalProgress   = $dataStat->sum('progress');
        $tanggalTerbaru  = $dataStat->max('tanggal_target');

        $chartLabels     = $dataStat->pluck('tanggal_target')->map(function($d) { return $d ? (is_string($d) ? (\Str::contains($d, 'T') ? \Str::before($d, 'T') : $d) : (method_exists($d, 'format') ? $d->format('Y-m-d') : $d)) : null; });
        $chartProgress   = $dataStat->pluck('progress');
        $chartAgenda     = $dataStat->pluck('agenda');

        return view('user.konten_tematik.index', compact(
            'data',
            'jumlahAgenda',
            'rataProgress',
            'totalProgress',
            'tanggalTerbaru',
            'chartLabels',
            'chartProgress',
            'chartAgenda',
            'adaFilter'
        ));
    }
}
