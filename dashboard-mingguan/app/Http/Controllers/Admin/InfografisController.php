<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infografis;
use Illuminate\Http\Request;

class InfografisController extends Controller
{
    public function index(Request $request)
    {
        $query = Infografis::query();

        // ================= SEARCH =================
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('periode', 'like', '%' . $request->search . '%')
                  ->orWhere('tanggal_target', 'like', '%' . $request->search . '%');
            });
        }

        // ================= FILTER MINGGU =================
        if ($request->filled('minggu')) {
            $query->whereRaw('WEEK(tanggal_target, 1) = ?', [$request->minggu]);
        }

        // ================= DATA + PAGINATION =================
        $data = $query->orderBy('tanggal_target', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        // ================= STATISTIK =================
        $jumlahSosial = Infografis::sum('sosial');
        $jumlahEkonomi = Infografis::sum('ekonomi');
        $jumlahPertanian = Infografis::sum('pertanian');
        $totalPeriode = Infografis::count();
        $tanggalTerbaru = Infografis::max('tanggal_target');

        return view('admin.infografis.index', compact(
            'data',
            'jumlahSosial',
            'jumlahEkonomi',
            'jumlahPertanian',
            'totalPeriode',
            'tanggalTerbaru'
        ));
    }

    public function create()
    {
        return view('admin.infografis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'periode'        => 'required|string|max:255',
            'sosial'         => 'required|string|max:100',
            'ekonomi'        => 'required|string|max:100',
            'pertanian'      => 'required|string|max:100',
            'link_bukti'     => 'nullable|string|max:255',
        ]);

        Infografis::create($request->all());

        return redirect()->route('admin.infografis.index')
            ->with('success', 'Data infografis berhasil disimpan');
    }

    public function edit($id)
    {
        $infografis = Infografis::findOrFail($id);
        return view('admin.infografis.edit', compact('infografis'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'periode'        => 'required|string',
            'sosial'         => 'required|string',
            'ekonomi'        => 'required|string',
            'pertanian'      => 'required|string',
            'link_bukti'     => 'nullable|string',
        ]);

        Infografis::findOrFail($id)->update($request->all());

        return redirect()->route('admin.infografis.index')
            ->with('success', 'Data infografis berhasil diperbarui');
    }

    public function destroy($id)
    {
        Infografis::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}
