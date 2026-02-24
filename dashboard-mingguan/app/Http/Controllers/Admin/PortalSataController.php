<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalSata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // rata-rata persentase jika kolom tersedia
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

        return view('admin.portal_sata.index', compact(
            'data', 'jumlahDataset', 'jumlahTargetTotal', 'jumlahCapaian', 'jumlahPersentase', 'tanggalTerbaru', 'chartLabels', 'chartDataset', 'chartCapaian'
        ));
    }

    public function create()
    {
        return view('admin.portal_sata.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'nama_dataset' => 'required|string|max:200',
            'dataset' => 'required|numeric|min:0',
            'target_total' => 'required|numeric|min:0',
            'capaian' => 'required|numeric|min:0',
        ], [
            'dataset.numeric' => 'Dataset harus berupa angka.',
            'dataset.min' => 'Dataset tidak boleh kurang dari 0.',
            'target_total.numeric' => 'Target total harus berupa angka.',
            'target_total.min' => 'Target total tidak boleh kurang dari 0.',
            'capaian.numeric' => 'Capaian harus berupa angka.',
            'capaian.min' => 'Capaian tidak boleh kurang dari 0.'
        ]);

        $data = $request->all();
        // Hitung persentase capaian
        $data['capaian(%)'] = ($data['target_total'] > 0) ? round(($data['capaian'] / $data['target_total']) * 100, 2) : 0;

        try {
            $data = $request->all();
            Log::info('PortalSata: Inserting data', $data);
            
            $result = PortalSata::create($data);
            
            Log::info('PortalSata: Data successfully saved with ID: ' . $result->id);
            
            return redirect()->route('admin.portal_sata.index')->with('success', 'Data Portal SATA tersimpan');
        } catch (\Exception $e) {
            Log::error('PortalSata: Failed to save data - ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $row = PortalSata::findOrFail($id);
        return view('admin.portal_sata.edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'nama_dataset' => 'required|string|max:200',
            'dataset' => 'required|numeric|min:0',
            'target_total' => 'required|numeric|min:0',
            'capaian' => 'required|numeric|min:0',
        ], [
            'dataset.numeric' => 'Dataset harus berupa angka.',
            'dataset.min' => 'Dataset tidak boleh kurang dari 0.',
            'target_total.numeric' => 'Target total harus berupa angka.',
            'target_total.min' => 'Target total tidak boleh kurang dari 0.',
            'capaian.numeric' => 'Capaian harus berupa angka.',
            'capaian.min' => 'Capaian tidak boleh kurang dari 0.'
        ]);

        $data = $request->all();
        // Hitung persentase capaian
        $data['capaian(%)'] = ($data['target_total'] > 0) ? round(($data['capaian'] / $data['target_total']) * 100, 2) : 0;

        try {
            $portalSata = PortalSata::findOrFail($id);
            $data = $request->all();
            
            Log::info('PortalSata: Updating data with ID: ' . $id, $data);
            
            $portalSata->update($data);
            
            Log::info('PortalSata: Data with ID ' . $id . ' successfully updated');
            
            return redirect()->route('admin.portal_sata.index')->with('success', 'Data Portal SATA diperbarui');
        } catch (\Exception $e) {
            Log::error('PortalSata: Failed to update data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            PortalSata::findOrFail($id)->delete();
            Log::info('PortalSata: Data with ID ' . $id . ' successfully deleted');
            return back()->with('success', 'Data dihapus');
        } catch (\Exception $e) {
            Log::error('PortalSata: Failed to delete data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
