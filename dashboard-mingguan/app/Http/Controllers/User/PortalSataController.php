<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PortalSata;
use Illuminate\Http\Request;

class PortalSataController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = PortalSata::query();

        if ($request->filled('search')) {
            $baseQuery->where(function($q) use ($request) {
                $q->where('dataset', 'like', '%' . $request->search . '%')
                  ->orWhere('target_total', 'like', '%' . $request->search . '%')
                  ->orWhere('capaian', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $baseQuery->whereBetween('tanggal_target', [
                $request->tanggal_awal,
                $request->tanggal_akhir,
            ]);
        }

        $tableQuery = clone $baseQuery;
        $data = $tableQuery->orderBy('tanggal_target', 'desc')->paginate(10)->withQueryString();

        $statQuery = clone $baseQuery;
        $dataStat = $statQuery->get();

        $jumlahDataset = $dataStat->count();
        $jumlahTargetTotal = $dataStat->sum(function ($item) {
            return is_numeric($item->target_total) ? (float)$item->target_total : floatval(preg_replace('/[^0-9.]/', '', $item->target_total));
        });
        $jumlahCapaian = $dataStat->sum(function ($item) {
            return is_numeric($item->capaian) ? (float)$item->capaian : floatval(preg_replace('/[^0-9.]/', '', $item->capaian));
        });
        $jumlahPersentase = 0;
        if ($dataStat->count() > 0) {
            $jumlahPersentase = $dataStat->avg(function ($item) {
                return isset($item->{'capaian(%)'}) ? (float)$item->{'capaian(%)'} : 0;
            });
        }

        $tanggalTerbaru = $dataStat->max('tanggal_target');

        $chartLabels = $dataStat->pluck('tanggal_target')->map(function ($d) { return $d ? $d->format('Y-m-d') : null; });
        $chartDataset = $dataStat->pluck('dataset')->map(function ($v) { return is_numeric($v) ? (float)$v : floatval(preg_replace('/[^0-9.]/', '', $v)); });
        $chartCapaian = $dataStat->pluck('capaian')->map(function ($v) { return is_numeric($v) ? (float)$v : floatval(preg_replace('/[^0-9.]/', '', $v)); });

        return view('user.portal_sata.index', compact(
            'data', 'jumlahDataset', 'jumlahTargetTotal', 'jumlahCapaian', 'jumlahPersentase', 'tanggalTerbaru', 'chartLabels', 'chartDataset', 'chartCapaian'
        ));
    }
}
