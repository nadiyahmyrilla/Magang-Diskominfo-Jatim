@extends('layouts.admin')

@section('content')

{{-- Success/Error Messages --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="dashboard-top mb-4">

    <div class="stat-grid">
        <div class="stat-card">
            <h6>JUMLAH DATASET</h6>
            <h2>{{ $jumlahDataset }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH TARGET TOTAL</h6>
            <h2>{{ $jumlahTargetTotal }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH CAPAIAN</h6>
            <h2>{{ $jumlahCapaian }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH PRESENTASE</h6>
            <h2>{{ number_format($jumlahPersentase, 2) }}%</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>
    </div>

    <div class="chart-card">
        <h5>Portal SATA</h5>
        <span>{{ $tanggalTerbaru }}</span>
        <canvas id="portalSataChart" height="180"></canvas>
    </div>
</div>

<div class="table-section">
    <div class="table-header align-items-start">
        <h4 class="mb-0">Portal SATA</h4>

        <div class="table-actions align-items-start">
            <a href="{{ route('admin.portal_sata.create') }}" class="btn btn-primary tambah-data-btn">Tambah Data +</a>

            {{-- FILTER FORM --}}
            <form method="GET" action="{{ route('admin.portal_sata.index') }}" class="row g-2 align-items-end ms-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cari Data</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik dataset, target, atau capaian" value="{{ request('search') }}">
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
                    <a href="{{ route('admin.portal_sata.index') }}" class="btn btn-outline-danger">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrapper mt-3">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Tanggal</th>
                    <th>Dataset</th>
                    <th>Target Total</th>
                    <th>Capaian</th>
                    <th>Persentase</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                <tr>
                    <td>{{ $row->nama_dataset }}</td>
                    <td>{{ optional($row->tanggal_target)->format('Y-m-d') }}</td>
                    <td>{{ $row->dataset }}</td>
                    <td>{{ $row->target_total }}</td>
                    <td>{{ $row->capaian }}</td>
                    <td>{{ $row->{'capaian(%)'} ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.portal_sata.edit', $row->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.portal_sata.destroy', $row->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Data tidak ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('portalSataChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'Dataset',
                data: @json($chartDataset),
                borderWidth: 2,
                tension: 0.4
            },
            {
                label: 'Capaian',
                data: @json($chartCapaian),
                borderWidth: 2,
                tension: 0.4
            }
        ]
    },
    options: { responsive: true }
});
</script>

@endsection
