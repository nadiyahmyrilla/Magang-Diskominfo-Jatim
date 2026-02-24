@extends('layouts.user')

@section('content')

{{-- ================== SECTION ATAS ================== --}}
<div class="dashboard-top mb-4">
    <div class="stat-grid">
        <div class="stat-card">
            <h6>AGENDA HARI INI</h6>
            <h2>{{ $jumlahHari }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>
        <div class="stat-card">
            <h6>AGENDA MINGGU INI</h6>
            <h2>{{ $jumlahMinggu }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>
        <div class="stat-card">
            <h6>AGENDA BULAN INI</h6>
            <h2>{{ $jumlahBulan }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>
        <div class="stat-card">
            <h6>AGENDA TAHUN INI</h6>
            <h2>{{ $jumlahTahun }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>
    </div>

    <div class="chart-card">
        <h5>Rekomendasi Statistik</h5>
        <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        <canvas id="rekomChart" height="180"></canvas>
    </div>
</div>

{{-- ================== SECTION TABEL ================== --}}
<div class="table-section">

    <div class="table-header align-items-start">
        <h4 class="mb-0">Rekomendasi Statistik</h4>
        <div class="table-actions align-items-start">
            <form method="GET" action="{{ route('user.rekomendasi_statistik.index') }}" class="row g-2 align-items-end ms-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cari Data</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik instansi atau kata kunci" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-secondary">Filter</button>
                    <a href="{{ route('user.rekomendasi_statistik.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrapper mt-3">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Instansi Tujuan</th>
                    <th>Batal</th>
                    <th>Layak</th>
                    <th>Pemeriksaan</th>
                    <th>Pengajuan</th>
                    <th>Pengesahan</th>
                    <th>Perbaikan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                <tr>
                    <td>{{ optional($row->tanggal_target)->format('Y-m-d') }}</td>
                    <td>{{ $row->instansi_tujuan }}</td>
                    <td>{{ $row->Batal }}</td>
                    <td>{{ $row->Layak }}</td>
                    <td>{{ $row->Pemeriksaan }}</td>
                    <td>{{ $row->Pengajuan }}</td>
                    <td>{{ $row->Pengesahan }}</td>
                    <td>{{ $row->Perbaikan }}</td>
                    <td>{{ $row->Total }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Data tidak ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('rekomChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json(collect($chartLabels)->map(fn($d) => $d ? (Str::contains($d, 'T') ? Str::before($d, 'T') : $d) : null)),
        datasets: [
            { label: 'Layak', data: @json($chartLayak), borderWidth: 2, tension: 0.4 },
            { label: 'Pemeriksaan', data: @json($chartPemeriksaan), borderWidth: 2, tension: 0.4 },
            { label: 'Pengajuan', data: @json($chartPengajuan), borderWidth: 2, tension: 0.4 }
        ]
    },
    options: { responsive: true }
});
</script>

@endsection
