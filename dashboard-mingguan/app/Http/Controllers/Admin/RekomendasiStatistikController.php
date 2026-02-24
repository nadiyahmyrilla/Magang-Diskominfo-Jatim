<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RekomendasiStatistik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RekomendasiStatistikController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = RekomendasiStatistik::query();

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

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $baseQuery->whereBetween('tanggal_target', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        $tableQuery = clone $baseQuery;
        $data = $tableQuery->orderBy('tanggal_target', 'desc')->paginate(10)->withQueryString();

        $statQuery = clone $baseQuery;
        $dataStat = $statQuery->get();

        // 4 stat cards: jumlah agenda satu hari, satu minggu, satu bulan, satu tahun
        $jumlahHari = $dataStat->filter(function ($item) {
            return $item->tanggal_target && $item->tanggal_target->isToday();
        })->count();

        $jumlahMinggu = $dataStat->filter(function ($item) {
            return $item->tanggal_target && $item->tanggal_target->greaterThan(now()->subWeek());
        })->count();

        $jumlahBulan = $dataStat->filter(function ($item) {
            return $item->tanggal_target && $item->tanggal_target->greaterThan(now()->subMonth());
        })->count();

        $jumlahTahun = $dataStat->filter(function ($item) {
            return $item->tanggal_target && $item->tanggal_target->greaterThan(now()->subYear());
        })->count();

        $tanggalTerbaru = $dataStat->max('tanggal_target');

        $chartLabels = $dataStat->pluck('tanggal_target')->map(function($d) { return $d ? (is_string($d) ? (\Str::contains($d, 'T') ? \Str::before($d, 'T') : $d) : (method_exists($d, 'format') ? $d->format('Y-m-d') : $d)) : null; });
        $chartLayak = $dataStat->pluck('Layak');
        $chartPemeriksaan = $dataStat->pluck('Pemeriksaan');
        $chartPengajuan = $dataStat->pluck('Pengajuan');

        return view('admin.rekomendasi_statistik.index', compact(
            'data', 'jumlahHari', 'jumlahMinggu', 'jumlahBulan', 'jumlahTahun', 'tanggalTerbaru', 'chartLabels', 'chartLayak', 'chartPemeriksaan', 'chartPengajuan'
        ));
    }

    public function create()
    {
        return view('admin.rekomendasi_statistik.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'instansi_tujuan' => 'required|string|max:150',
            'Batal' => 'required|numeric|min:0',
            'Layak' => 'required|numeric|min:0',
            'Pemeriksaan' => 'required|numeric|min:0',
            'Pengajuan' => 'required|numeric|min:0',
            'Pengesahan' => 'required|numeric|min:0',
            'Perbaikan' => 'required|numeric|min:0',
        ], [
            'Batal.numeric' => 'Batal harus berupa angka.',
            'Batal.min' => 'Batal tidak boleh kurang dari 0.',
            'Layak.numeric' => 'Layak harus berupa angka.',
            'Layak.min' => 'Layak tidak boleh kurang dari 0.',
            'Pemeriksaan.numeric' => 'Pemeriksaan harus berupa angka.',
            'Pemeriksaan.min' => 'Pemeriksaan tidak boleh kurang dari 0.',
            'Pengajuan.numeric' => 'Pengajuan harus berupa angka.',
            'Pengajuan.min' => 'Pengajuan tidak boleh kurang dari 0.',
            'Pengesahan.numeric' => 'Pengesahan harus berupa angka.',
            'Pengesahan.min' => 'Pengesahan tidak boleh kurang dari 0.',
            'Perbaikan.numeric' => 'Perbaikan harus berupa angka.',
            'Perbaikan.min' => 'Perbaikan tidak boleh kurang dari 0.'
        ]);

        $data = $request->all();
        $data['Total'] = $data['Batal'] + $data['Layak'] + $data['Pemeriksaan'] + $data['Pengajuan'] + $data['Pengesahan'] + $data['Perbaikan'];

        try {
            $data = $request->all();
            Log::info('RekomendasiStatistik: Inserting data', $data);
            
            $result = RekomendasiStatistik::create($data);
            
            Log::info('RekomendasiStatistik: Data successfully saved with ID: ' . $result->id);
            
            return redirect()->route('admin.rekomendasi_statistik.index')->with('success', 'Data tersimpan');
        } catch (\Exception $e) {
            Log::error('RekomendasiStatistik: Failed to save data - ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $row = RekomendasiStatistik::findOrFail($id);
        return view('admin.rekomendasi_statistik.edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'instansi_tujuan' => 'required|string|max:150',
            'Batal' => 'required|numeric|min:0',
            'Layak' => 'required|numeric|min:0',
            'Pemeriksaan' => 'required|numeric|min:0',
            'Pengajuan' => 'required|numeric|min:0',
            'Pengesahan' => 'required|numeric|min:0',
            'Perbaikan' => 'required|numeric|min:0',
        ], [
            'Batal.numeric' => 'Batal harus berupa angka.',
            'Batal.min' => 'Batal tidak boleh kurang dari 0.',
            'Layak.numeric' => 'Layak harus berupa angka.',
            'Layak.min' => 'Layak tidak boleh kurang dari 0.',
            'Pemeriksaan.numeric' => 'Pemeriksaan harus berupa angka.',
            'Pemeriksaan.min' => 'Pemeriksaan tidak boleh kurang dari 0.',
            'Pengajuan.numeric' => 'Pengajuan harus berupa angka.',
            'Pengajuan.min' => 'Pengajuan tidak boleh kurang dari 0.',
            'Pengesahan.numeric' => 'Pengesahan harus berupa angka.',
            'Pengesahan.min' => 'Pengesahan tidak boleh kurang dari 0.',
            'Perbaikan.numeric' => 'Perbaikan harus berupa angka.',
            'Perbaikan.min' => 'Perbaikan tidak boleh kurang dari 0.'
        ]);

        $data = $request->all();
        $data['Total'] = $data['Batal'] + $data['Layak'] + $data['Pemeriksaan'] + $data['Pengajuan'] + $data['Pengesahan'] + $data['Perbaikan'];

        try {
            $rekomendasiStatistik = RekomendasiStatistik::findOrFail($id);
            $data = $request->all();
            
            Log::info('RekomendasiStatistik: Updating data with ID: ' . $id, $data);
            
            $rekomendasiStatistik->update($data);
            
            Log::info('RekomendasiStatistik: Data with ID ' . $id . ' successfully updated');
            
            return redirect()->route('admin.rekomendasi_statistik.index')->with('success', 'Data diperbarui');
        } catch (\Exception $e) {
            Log::error('RekomendasiStatistik: Failed to update data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            RekomendasiStatistik::findOrFail($id)->delete();
            Log::info('RekomendasiStatistik: Data with ID ' . $id . ' successfully deleted');
            return back()->with('success', 'Data dihapus');
        } catch (\Exception $e) {
            Log::error('RekomendasiStatistik: Failed to delete data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
