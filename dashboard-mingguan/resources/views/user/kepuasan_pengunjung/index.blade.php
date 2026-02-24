@extends('layouts.user')

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

{{-- ================== SECTION ATAS ================== --}}
<div class="dashboard-top mb-4">

    <div class="stat-grid">
        <div class="stat-card">
            <h6>JUMLAH SANGAT PUAS</h6>
            <h2>{{ $jumlahSangatPuas ?? 0 }}</h2>
            <span>{{ $tanggalTerbaru?->format('d M Y') ?? 'N/A' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH PUAS</h6>
            <h2>{{ $jumlahPuas ?? 0 }}</h2>
            <span>{{ $tanggalTerbaru?->format('d M Y') ?? 'N/A' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH TIDAK PUAS</h6>
            <h2>{{ $jumlahTidakPuas ?? 0 }}</h2>
            <span>{{ $tanggalTerbaru?->format('d M Y') ?? 'N/A' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH SANGAT TIDAK PUAS</h6>
            <h2>{{ $jumlahSangatTidakPuas ?? 0 }}</h2>
            <span>{{ $tanggalTerbaru?->format('d M Y') ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="chart-card">
        <h5>Kepuasan Pengunjung</h5>
        <span>{{ $tanggalTerbaru?->format('d M Y') ?? 'N/A' }}</span>
        <canvas id="kepuasanChart" height="180"></canvas>
    </div>
</div>

{{-- ================== SECTION TABEL ================== --}}
<div class="table-section">

    <div class="table-header align-items-start">
        <h4 class="mb-0">Data Kepuasan Pengunjung</h4>

        <div class="table-actions align-items-start">
            {{-- ================= FORM FILTER ================= --}}
            <form method="GET"
                  action="{{ route('user.kepuasan_pengunjung.index') }}"
                  class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cari Data</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Ketik jenis kelamin atau tanggal"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal Awal</label>
                    <input type="date"
                           name="tanggal_awal"
                           class="form-control"
                           value="{{ request('tanggal_awal') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal Akhir</label>
                    <input type="date"
                           name="tanggal_akhir"
                           class="form-control"
                           value="{{ request('tanggal_akhir') }}">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-secondary w-100">
                        Filter
                    </button>

                    <a href="{{ route('user.kepuasan_pengunjung.index') }}"
                       class="btn btn-outline-danger w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrapper mt-3">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tanggal Pelaporan</th>
                    <th>Jenis Kelamin</th>
                    <th>Sangat Puas</th>
                    <th>Puas</th>
                    <th>Tidak Puas</th>
                    <th>Sangat Tidak Puas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                <tr>
                    <td>{{ $row->tanggal_target }}</td>
                    <td>{{ $row->jenis_kelamin }}</td>
                    <td>{{ $row->sangat_puas }}</td>
                    <td>{{ $row->puas }}</td>
                    <td>{{ $row->tidak_puas }}</td>
                    <td>{{ $row->sangat_tidak_puas }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Data tidak ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $data->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('kepuasanChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels ?? []),
        datasets: [
            {
                label: 'Sangat Puas',
                data: @json($chartSangatPuas ?? []),
                borderWidth: 2,
                tension: 0.4
            },
            {
                label: 'Puas',
                data: @json($chartPuas ?? []),
                borderWidth: 2,
                tension: 0.4
            },
            {
                label: 'Tidak Puas',
                data: @json($chartTidakPuas ?? []),
                borderWidth: 2,
                tension: 0.4
            },
            {
                label: 'Sangat Tidak Puas',
                data: @json($chartSangatTidakPuas ?? []),
                borderWidth: 2,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

@endsection
