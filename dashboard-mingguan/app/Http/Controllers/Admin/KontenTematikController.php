<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontenTematik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KontenTematikController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | QUERY DASAR
        |--------------------------------------------------------------------------
        */
        $baseQuery = KontenTematik::query();

        /*
        |--------------------------------------------------------------------------
        | FILTER SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('agenda', 'like', '%' . $request->search . '%')
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
            $tanggalTerbaru = KontenTematik::max('tanggal_target');
            $statQuery->whereDate('tanggal_target', $tanggalTerbaru);
        }

        $dataStat = $statQuery->get();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */
        $jumlahAgenda    = $dataStat->count();
        $rataProgress    = $dataStat->count() > 0 ? $dataStat->avg('progress') : 0;
        $totalProgress   = $dataStat->sum('progress');
        $tanggalTerbaru  = $dataStat->max('tanggal_target');

        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK
        |--------------------------------------------------------------------------
        */
        $chartLabels     = $dataStat->pluck('tanggal_target')->map(function($d) { return $d ? (is_string($d) ? (\Str::contains($d, 'T') ? \Str::before($d, 'T') : $d) : (method_exists($d, 'format') ? $d->format('Y-m-d') : $d)) : null; });
        $chartProgress   = $dataStat->pluck('progress');
        $chartAgenda     = $dataStat->pluck('agenda');

        return view('admin.konten_tematik.index', compact(
            'data',
            'jumlahAgenda',
            'rataProgress',
            'totalProgress',
            'tanggalTerbaru',
            'chartLabels',
            'chartProgress',
            'chartAgenda'
        ));
    }

    public function create()
    {
        return view('admin.konten_tematik.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'agenda'         => 'required|string|max:200',
            'progress'       => 'required|numeric|min:0|max:100',
            'data_dukung'    => 'nullable|string|max:255',
        ]);

        try {
            $data = $request->all();
            Log::info('KontenTematik: Inserting data', $data);
            
            $result = KontenTematik::create($data);
            
            Log::info('KontenTematik: Data successfully saved with ID: ' . $result->id);
            
            return redirect()->route('admin.konten_tematik.index')
                ->with('success', 'Data konten tematik berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('KontenTematik: Failed to save data - ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $kontenTematik = KontenTematik::findOrFail($id);
        return view('admin.konten_tematik.edit', compact('kontenTematik'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'agenda'         => 'required|string',
            'progress'       => 'required|numeric|min:0|max:100',
            'data_dukung'    => 'nullable|string',
        ]);

        try {
            $kontenTematik = KontenTematik::findOrFail($id);
            $data = $request->all();
            
            Log::info('KontenTematik: Updating data with ID: ' . $id, $data);
            
            $kontenTematik->update($data);
            
            Log::info('KontenTematik: Data with ID ' . $id . ' successfully updated');
            
            return redirect()->route('admin.konten_tematik.index')
                ->with('success', 'Data konten tematik berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('KontenTematik: Failed to update data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            KontenTematik::findOrFail($id)->delete();
            Log::info('KontenTematik: Data with ID ' . $id . ' successfully deleted');
            return back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('KontenTematik: Failed to delete data with ID ' . $id . ' - ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
