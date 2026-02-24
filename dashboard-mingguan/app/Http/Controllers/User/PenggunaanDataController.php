<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PenggunaanData;
use Illuminate\Http\Request;

class PenggunaanDataController extends Controller
{


    public function index(Request $request)
    {
        $baseQuery = PenggunaanData::query();

        // Filter search (periode_awal, periode_akhir as in admin)
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->whereDate('periode_awal', 'like', '%' . $request->search . '%')
                  ->orWhereDate('periode_akhir', 'like', '%' . $request->search . '%');
            });
        }

        // Filter tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $baseQuery->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        $adaFilter = $request->filled('search') || ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir'));

        // Table data
        $tableQuery = clone $baseQuery;
        $data = $tableQuery->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        // Stats & chart
        $statQuery = clone $baseQuery;
        if (!$adaFilter) {
            $tanggalTerbaru = PenggunaanData::max('tanggal');
            $statQuery->whereDate('tanggal', $tanggalTerbaru);
        }
        $dataStat = $statQuery->get();

        $jumlahView     = $dataStat->sum('view');
        $jumlahDownload = $dataStat->sum('download');
        $jumlahPeriode  = $dataStat->count();
        $jumlahTotal    = $jumlahView + $jumlahDownload;
        $tanggalTerbaru = $dataStat->max('tanggal');

        $chartLabels    = $dataStat->pluck('tanggal')->map(function ($date) {
            return $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : (string) $date;
        });
        $chartView      = $dataStat->pluck('view');
        $chartDownload  = $dataStat->pluck('download');

        return view('user.penggunaan_data.index', compact(
            'data',
            'jumlahView',
            'jumlahDownload',
            'jumlahPeriode',
            'jumlahTotal',
            'tanggalTerbaru',
            'chartLabels',
            'chartView',
            'chartDownload',
            'adaFilter'
        ));
    }
}
