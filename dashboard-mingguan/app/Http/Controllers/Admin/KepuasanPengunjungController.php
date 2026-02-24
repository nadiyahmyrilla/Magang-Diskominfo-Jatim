<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KepuasanPengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KepuasanPengunjungController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | QUERY DASAR
        |--------------------------------------------------------------------------
        */
        $baseQuery = KepuasanPengunjung::query();

        /*
        |--------------------------------------------------------------------------
        | FILTER SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('jenis_kelamin', 'like', '%' . $request->search . '%')
                ->orWhere('tanggal_target', 'like', '%' . $request->search . '%');
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
        | Jika TIDAK ADA FILTER → tampilkan SEMUA DATA
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
        | Jika TIDAK ADA FILTER → ambil tanggal terbaru saja
        */
        $statQuery = clone $baseQuery;

        if (!$adaFilter) {
            $tanggalTerbaru = KepuasanPengunjung::max('tanggal_target');
            $statQuery->whereDate('tanggal_target', $tanggalTerbaru);
        }

        $dataStat = $statQuery->get();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */
        $jumlahSangatPuas    = $dataStat->sum('sangat_puas');
        $jumlahPuas          = $dataStat->sum('puas');
        $jumlahTidakPuas     = $dataStat->sum('tidak_puas');
        $jumlahSangatTidakPuas = $dataStat->sum('sangat_tidak_puas');
        $totalData           = $dataStat->count();
        $tanggalTerbaru      = $dataStat->max('tanggal_target');

        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK
        |--------------------------------------------------------------------------
        */
        $chartLabels           = $dataStat->pluck('tanggal_target');
        $chartSangatPuas       = $dataStat->pluck('sangat_puas');
        $chartPuas             = $dataStat->pluck('puas');
        $chartTidakPuas        = $dataStat->pluck('tidak_puas');
        $chartSangatTidakPuas  = $dataStat->pluck('sangat_tidak_puas');

        return view('admin.kepuasan_pengunjung.index', compact(
            'data',
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

    public function create()
    {
        return view('admin.kepuasan_pengunjung.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_target'       => 'required|date',
            'jenis_kelamin'        => 'required|in:Laki - Laki,Perempuan',
            'sangat_puas'          => 'required|numeric|min:0',
            'puas'                 => 'required|numeric|min:0',
            'tidak_puas'           => 'required|numeric|min:0',
            'sangat_tidak_puas'    => 'required|numeric|min:0',
        ], [
            'jenis_kelamin.in' => 'Jenis kelamin harus dipilih dari daftar.',
            'sangat_puas.numeric' => 'Jumlah sangat puas harus berupa angka.',
            'sangat_puas.min' => 'Jumlah sangat puas tidak boleh kurang dari 0.',
            'puas.numeric' => 'Jumlah puas harus berupa angka.',
            'puas.min' => 'Jumlah puas tidak boleh kurang dari 0.',
            'tidak_puas.numeric' => 'Jumlah tidak puas harus berupa angka.',
            'tidak_puas.min' => 'Jumlah tidak puas tidak boleh kurang dari 0.',
            'sangat_tidak_puas.numeric' => 'Jumlah sangat tidak puas harus berupa angka.',
            'sangat_tidak_puas.min' => 'Jumlah sangat tidak puas tidak boleh kurang dari 0.'
        ]);

        try {
            $data = $request->all();
            Log::info('KepuasanPengunjung: Inserting data', $data);
            
            $result = KepuasanPengunjung::create($data);
            
            Log::info('KepuasanPengunjung: Data successfully saved with ID: ' . $result->id);
            
            return redirect()->route('admin.kepuasan_pengunjung.index')
                ->with('success', 'Data kepuasan pengunjung berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('KepuasanPengunjung: Failed to save data - ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $kepuasanPengunjung = KepuasanPengunjung::findOrFail($id);
        return view('admin.kepuasan_pengunjung.edit', compact('kepuasanPengunjung'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_target'       => 'required|date',
            'jenis_kelamin'        => 'required|in:Laki - Laki,Perempuan',
            'sangat_puas'          => 'required|numeric|min:0',
            'puas'                 => 'required|numeric|min:0',
            'tidak_puas'           => 'required|numeric|min:0',
            'sangat_tidak_puas'    => 'required|numeric|min:0',
        ], [
            'jenis_kelamin.in' => 'Jenis kelamin harus dipilih dari daftar.',
            'sangat_puas.numeric' => 'Jumlah sangat puas harus berupa angka.',
            'sangat_puas.min' => 'Jumlah sangat puas tidak boleh kurang dari 0.',
            'puas.numeric' => 'Jumlah puas harus berupa angka.',
            'puas.min' => 'Jumlah puas tidak boleh kurang dari 0.',
            'tidak_puas.numeric' => 'Jumlah tidak puas harus berupa angka.',
            'tidak_puas.min' => 'Jumlah tidak puas tidak boleh kurang dari 0.',
            'sangat_tidak_puas.numeric' => 'Jumlah sangat tidak puas harus berupa angka.',
            'sangat_tidak_puas.min' => 'Jumlah sangat tidak puas tidak boleh kurang dari 0.'
        ]);

        try {
            $kepuasanPengunjung = KepuasanPengunjung::findOrFail($id);
            $data = $request->all();
            
            Log::info('KepuasanPengunjung: Updating data with ID: ' . $id, $data);
            
            $kepuasanPengunjung->update($data);
            
            Log::info('KepuasanPengunjung: Data with ID ' . $id . ' successfully updated');
            
            return redirect()->route('admin.kepuasan_pengunjung.index')
                ->with('success', 'Data kepuasan pengunjung berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('KepuasanPengunjung: Failed to update data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            KepuasanPengunjung::findOrFail($id)->delete();
            Log::info('KepuasanPengunjung: Data with ID ' . $id . ' successfully deleted');
            return back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('KepuasanPengunjung: Failed to delete data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
