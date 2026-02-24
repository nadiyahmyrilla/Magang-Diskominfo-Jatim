<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenggunaanData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PenggunaanDataController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | QUERY DASAR
        |--------------------------------------------------------------------------
        */
        $baseQuery = PenggunaanData::query();

        /*
        |--------------------------------------------------------------------------
        | FILTER SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->whereDate('periode_awal', 'like', '%' . $request->search . '%')
                  ->orWhereDate('periode_akhir', 'like', '%' . $request->search . '%');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER TANGGAL
        |--------------------------------------------------------------------------
        */
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $baseQuery->whereBetween('tanggal', [
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
            ->orderBy('tanggal', 'desc')
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
            $tanggalT = PenggunaanData::max('tanggal');
            $statQuery->whereDate('tanggal', $tanggalT);
        }

        $dataStat = $statQuery->get();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */
        $jumlahView     = $dataStat->sum('view');
        $jumlahDownload = $dataStat->sum('download');
        $jumlahPeriode  = $dataStat->count();
        $jumlahTotal    = $jumlahView + $jumlahDownload;
        $tanggalTerbaru = $dataStat->max('tanggal');

        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK
        |--------------------------------------------------------------------------
        */
        $chartLabels    = $dataStat->pluck('tanggal')->map(function ($date) {
            return $date->format('Y-m-d');
        });
        $chartView      = $dataStat->pluck('view');
        $chartDownload  = $dataStat->pluck('download');

        return view('admin.penggunaan_data.index', compact(
            'data',
            'jumlahView',
            'jumlahDownload',
            'jumlahPeriode',
            'jumlahTotal',
            'tanggalTerbaru',
            'chartLabels',
            'chartView',
            'chartDownload'
        ));
    }

    public function create()
    {
        return view('admin.penggunaan_data.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'view'         => 'required|numeric|min:0',
            'download'     => 'required|numeric|min:0',
        ], [
            'view.numeric' => 'Jumlah view harus berupa angka.',
            'view.min' => 'Jumlah view tidak boleh kurang dari 0.',
            'download.numeric' => 'Jumlah download harus berupa angka.',
            'download.min' => 'Jumlah download tidak boleh kurang dari 0.'
        ]);

        try {
            // Pastikan `tanggal` otomatis diisi tanggal hari ini jika tidak diberikan
            $data = $request->all();
            if (empty($data['tanggal'])) {
                $data['tanggal'] = now()->format('Y-m-d');
            }

            Log::info('PenggunaanData: Inserting data', $data);
            
            $result = PenggunaanData::create($data);
            
            Log::info('PenggunaanData: Data successfully saved with ID: ' . $result->id);
            
            return redirect()->route('admin.penggunaan_data.index')
                ->with('success', 'Data penggunaan data berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('PenggunaanData: Failed to save data - ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $penggunaanData = PenggunaanData::findOrFail($id);
        return view('admin.penggunaan_data.edit', compact('penggunaanData'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'view'         => 'required|numeric|min:0',
            'download'     => 'required|numeric|min:0',
        ], [
            'view.numeric' => 'Jumlah view harus berupa angka.',
            'view.min' => 'Jumlah view tidak boleh kurang dari 0.',
            'download.numeric' => 'Jumlah download harus berupa angka.',
            'download.min' => 'Jumlah download tidak boleh kurang dari 0.'
        ]);

        try {
            $penggunaanData = PenggunaanData::findOrFail($id);
            $data = $request->all();
            
            Log::info('PenggunaanData: Updating data with ID: ' . $id, $data);
            
            $penggunaanData->update($data);
            
            Log::info('PenggunaanData: Data with ID ' . $id . ' successfully updated');
            
            return redirect()->route('admin.penggunaan_data.index')
                ->with('success', 'Data penggunaan data berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('PenggunaanData: Failed to update data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            PenggunaanData::findOrFail($id)->delete();
            Log::info('PenggunaanData: Data with ID ' . $id . ' successfully deleted');
            return back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('PenggunaanData: Failed to delete data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
