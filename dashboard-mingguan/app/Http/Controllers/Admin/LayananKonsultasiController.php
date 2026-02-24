<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananKonsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LayananKonsultasiController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | QUERY DASAR
        |--------------------------------------------------------------------------
        */
        $baseQuery = LayananKonsultasi::query();

        /*
        |--------------------------------------------------------------------------
        | FILTER SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('perangkat_daerah', 'like', '%' . $request->search . '%')
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
            $tanggalTerbaru = LayananKonsultasi::max('tanggal_target');
            $statQuery->whereDate('tanggal_target', $tanggalTerbaru);
        }

        $dataStat = $statQuery->get();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */
        $jumlahLakiLaki         = $dataStat->sum('laki_laki');
        $jumlahPerempuan        = $dataStat->sum('perempuan');
        $jumlahPerangkatDaerah  = $dataStat->count();
        $jumlahTotal            = $jumlahLakiLaki + $jumlahPerempuan;
        $tanggalTerbaru         = $dataStat->max('tanggal_target');

        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK
        |--------------------------------------------------------------------------
        */
        $chartLabels     = $dataStat->pluck('tanggal_target')->map(function ($date) {
            return $date->format('Y-m-d');
        });
        $chartLakiLaki   = $dataStat->pluck('laki_laki');
        $chartPerempuan  = $dataStat->pluck('perempuan');

        return view('admin.layanan_konsultasi.index', compact(
            'data',
            'jumlahLakiLaki',
            'jumlahPerempuan',
            'jumlahPerangkatDaerah',
            'jumlahTotal',
            'tanggalTerbaru',
            'chartLabels',
            'chartLakiLaki',
            'chartPerempuan'
        ));
    }

    public function create()
    {
        return view('admin.layanan_konsultasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_target'    => 'required|date',
            'perangkat_daerah'  => 'required|string|max:250',
            'laki_laki'         => 'required|numeric|min:0',
            'perempuan'         => 'required|numeric|min:0',
        ], [
            'laki_laki.numeric' => 'Jumlah laki-laki harus berupa angka.',
            'laki_laki.min' => 'Jumlah laki-laki tidak boleh kurang dari 0.',
            'perempuan.numeric' => 'Jumlah perempuan harus berupa angka.',
            'perempuan.min' => 'Jumlah perempuan tidak boleh kurang dari 0.'
        ]);

        try {
            $data = $request->all();
            Log::info('LayananKonsultasi: Inserting data', $data);
            
            $result = LayananKonsultasi::create($data);
            
            Log::info('LayananKonsultasi: Data successfully saved with ID: ' . $result->id);
            
            return redirect()->route('admin.layanan_konsultasi.index')
                ->with('success', 'Data layanan konsultasi berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('LayananKonsultasi: Failed to save data - ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $layananKonsultasi = LayananKonsultasi::findOrFail($id);
        return view('admin.layanan_konsultasi.edit', compact('layananKonsultasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_target'    => 'required|date',
            'perangkat_daerah'  => 'required|string',
            'laki_laki'         => 'required|numeric|min:0',
            'perempuan'         => 'required|numeric|min:0',
        ], [
            'laki_laki.numeric' => 'Jumlah laki-laki harus berupa angka.',
            'laki_laki.min' => 'Jumlah laki-laki tidak boleh kurang dari 0.',
            'perempuan.numeric' => 'Jumlah perempuan harus berupa angka.',
            'perempuan.min' => 'Jumlah perempuan tidak boleh kurang dari 0.'
        ]);

        try {
            $layananKonsultasi = LayananKonsultasi::findOrFail($id);
            $data = $request->all();
            
            Log::info('LayananKonsultasi: Updating data with ID: ' . $id, $data);
            
            $layananKonsultasi->update($data);
            
            Log::info('LayananKonsultasi: Data with ID ' . $id . ' successfully updated');
            
            return redirect()->route('admin.layanan_konsultasi.index')
                ->with('success', 'Data layanan konsultasi berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('LayananKonsultasi: Failed to update data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            LayananKonsultasi::findOrFail($id)->delete();
            Log::info('LayananKonsultasi: Data with ID ' . $id . ' successfully deleted');
            return back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('LayananKonsultasi: Failed to delete data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
