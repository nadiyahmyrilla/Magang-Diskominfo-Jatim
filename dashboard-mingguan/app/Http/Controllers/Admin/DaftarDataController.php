<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DaftarData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DaftarDataController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | QUERY DASAR
        |--------------------------------------------------------------------------
        */
        $baseQuery = DaftarData::query();

        /*
        |--------------------------------------------------------------------------
        | FILTER SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('perangkat_daerah', 'like', '%' . $request->search . '%');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER TANGGAL
        |--------------------------------------------------------------------------
        */
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $baseQuery->whereBetween('tanggal_target', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK APAKAH ADA FILTER
        |--------------------------------------------------------------------------
        */
        $adaFilter = $request->filled('search')
            || ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir'));

        /*
        |--------------------------------------------------------------------------
        | QUERY UNTUK TABEL
        |--------------------------------------------------------------------------
        */
        $tableQuery = clone $baseQuery;

        $data = $tableQuery
            ->orderBy('tanggal_target', 'desc')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | QUERY UNTUK STATISTIK & GRAFIK
        |--------------------------------------------------------------------------
        */
        $statQuery = clone $baseQuery;

        if (!$adaFilter) {
            $tanggalT = DaftarData::max('tanggal_target');
            $statQuery->whereDate('tanggal_target', $tanggalT);
        }

        $dataStat = $statQuery->get();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */
        $totalJumlah         = $dataStat->sum('jumlah');
        $totalPerangkat      = $dataStat->count();
        $rataRataJumlah      = $totalPerangkat > 0 ? round($totalJumlah / $totalPerangkat, 2) : 0;
        $tanggalTerbaru      = $dataStat->max('tanggal_target');

        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK
        |--------------------------------------------------------------------------
        */
        $chartLabels    = $dataStat->pluck('perangkat_daerah');
        $chartJumlah    = $dataStat->pluck('jumlah');

        return view('admin.daftar_data.index', compact(
            'data',
            'totalJumlah',
            'totalPerangkat',
            'rataRataJumlah',
            'tanggalTerbaru',
            'chartLabels',
            'chartJumlah'
        ));
    }

    public function create()
    {
        return view('admin.daftar_data.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'perangkat_daerah' => 'required|string|max:255',
            'jumlah'           => 'required|numeric|min:0',
            'tanggal_target'   => 'required|date',
        ], [
            'jumlah.numeric' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0.'
        ]);

        try {
            $data = $request->all();
            Log::info('DaftarData: Inserting data', $data);
            
            $result = DaftarData::create($data);
            
            Log::info('DaftarData: Data successfully saved with ID: ' . $result->id);
            
            return redirect()->route('admin.daftar_data.index')
                ->with('success', 'Data daftar data berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('DaftarData: Failed to save data - ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $daftarData = DaftarData::findOrFail($id);
        return view('admin.daftar_data.edit', compact('daftarData'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'perangkat_daerah' => 'required|string|max:255',
            'jumlah'           => 'required|numeric|min:0',
            'tanggal_target'   => 'required|date',
        ], [
            'jumlah.numeric' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0.'
        ]);

        try {
            $daftarData = DaftarData::findOrFail($id);
            $data = $request->all();
            
            Log::info('DaftarData: Updating data with ID: ' . $id, $data);
            
            $daftarData->update($data);
            
            Log::info('DaftarData: Data with ID ' . $id . ' successfully updated');
            
            return redirect()->route('admin.daftar_data.index')
                ->with('success', 'Data daftar data berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('DaftarData: Failed to update data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DaftarData::findOrFail($id)->delete();
            Log::info('DaftarData: Data with ID ' . $id . ' successfully deleted');
            return back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('DaftarData: Failed to delete data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
