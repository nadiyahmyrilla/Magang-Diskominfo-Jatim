













@extends('layouts.user')

@section('content')

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
            <form method="GET" action="{{ route('user.portal_sata.index') }}" class="row g-2 align-items-end ms-3">
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
                    <a href="{{ route('user.portal_sata.index') }}" class="btn btn-outline-danger">Reset</a>
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
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Data tidak ditemukan</td>
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
