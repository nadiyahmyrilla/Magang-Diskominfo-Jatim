<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KepuasanPengunjung;
use Illuminate\Http\Request;

class KepuasanPengunjungController extends Controller
{
    public function index(Request $request)
    {
        // Mirror admin logic but read-only for user
        $baseQuery = KepuasanPengunjung::query();

        // Filter search (match admin: jenis_kelamin or tanggal_target)
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('jenis_kelamin', 'like', '%' . $request->search . '%')
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

        // Table data (paginated)
        $tableQuery = clone $baseQuery;
        $data = $tableQuery->orderBy('tanggal_target', 'desc')->paginate(10)->withQueryString();

        // Stats & chart (if no filter, use latest date only)
        $statQuery = clone $baseQuery;
        if (!$adaFilter) {
            $tanggalTerbaru = KepuasanPengunjung::max('tanggal_target');
            if ($tanggalTerbaru) {
                $statQuery->whereDate('tanggal_target', $tanggalTerbaru);
            }
        }

        $dataStat = $statQuery->get();

        $jumlahSangatPuas = $dataStat->sum('sangat_puas');
        $jumlahPuas = $dataStat->sum('puas');
        $jumlahTidakPuas = $dataStat->sum('tidak_puas');
        $jumlahSangatTidakPuas = $dataStat->sum('sangat_tidak_puas');
        $totalData = $dataStat->count();
        $tanggalTerbaru = $dataStat->max('tanggal_target');

        // Ensure $tanggalTerbaru is a Carbon instance for the view
        if ($tanggalTerbaru && !($tanggalTerbaru instanceof \Carbon\Carbon)) {
            try {
                $tanggalTerbaru = \Carbon\Carbon::parse($tanggalTerbaru);
            } catch (\Exception $e) {
                $tanggalTerbaru = null;
            }
        }

        // Normalize chart labels to 'Y-m-d' strings, handling Carbon or string dates
        $chartLabels = $dataStat->pluck('tanggal_target')->map(function ($d) {
            if (!$d) return null;
            if ($d instanceof \Carbon\Carbon) return $d->format('Y-m-d');
            try {
                return \Carbon\Carbon::parse($d)->format('Y-m-d');
            } catch (\Exception $e) {
                return (string) $d;
            }
        });
        $chartSangatPuas = $dataStat->pluck('sangat_puas');
        $chartPuas = $dataStat->pluck('puas');
        $chartTidakPuas = $dataStat->pluck('tidak_puas');
        $chartSangatTidakPuas = $dataStat->pluck('sangat_tidak_puas');

        return view('user.kepuasan_pengunjung.index', compact(
            'data',
            'adaFilter',
            'jumlahSangatPuas',
            'jumlahPuas',
            'jumlahTidakPuas',
            'jumlahSangatTidakPuas',
            'totalData',
            'tanggalTerbaru',
            'chartLabels',
            'chartSangatPuas',
            'chartPuas',
            'chartTidakPuas',
            'chartSangatTidakPuas'
        ));
    }
}
