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

{{-- ================== SECTION ATAS ================== --}}
<div class="dashboard-top mb-4">

    <div class="stat-grid">
        <div class="stat-card">
            <h6>TOTAL JUMLAH</h6>
            <h2>{{ $totalJumlah }}</h2>
            <span>{{ $tanggalTerbaru ? $tanggalTerbaru->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>TOTAL PERANGKAT</h6>
            <h2>{{ $totalPerangkat }}</h2>
            <span>{{ $tanggalTerbaru ? $tanggalTerbaru->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>RATA-RATA JUMLAH</h6>
            <h2>{{ $rataRataJumlah }}</h2>
            <span>{{ $tanggalTerbaru ? $tanggalTerbaru->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>TANGGAL TERBARU</h6>
            <h2>{{ $tanggalTerbaru ? $tanggalTerbaru->format('d') : '-' }}</h2>
            <span>{{ $tanggalTerbaru ? $tanggalTerbaru->format('Y-m-d') : '-' }}</span>
        </div>
    </div>

    <div class="chart-card">
        <h5>Daftar Data</h5>
        <span>{{ $tanggalTerbaru ? $tanggalTerbaru->format('Y-m-d') : '-' }}</span>
        <canvas id="daftarDataChart" height="180"></canvas>
    </div>
</div>

{{-- ================== SECTION TABEL ================== --}}
<div class="table-section">

    <div class="table-header align-items-start">
        <h4 class="mb-0">Daftar Data</h4>

        <div class="table-actions align-items-start">
            <a href="{{ route('admin.daftar_data.create') }}" class="btn btn-primary tambah-data-btn">
                Tambah Data +
            </a>

            {{-- ================= FORM FILTER ================= --}}
            <form method="GET"
                  action="{{ route('admin.daftar_data.index') }}"
                  class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cari Data</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Ketik perangkat daerah"
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

                    <a href="{{ route('admin.daftar_data.index') }}"
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
                    <th>Perangkat Daerah</th>
                    <th>Jumlah</th>
                    <th>Tanggal Target</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                <tr>
                    <td>{{ $row->perangkat_daerah }}</td>
                    <td>{{ $row->jumlah }}</td>
                    <td>{{ $row->tanggal_target->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.daftar_data.edit', $row->id) }}"
                           class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('admin.daftar_data.destroy', $row->id) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">
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
const ctx = document.getElementById('daftarDataChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'Jumlah',
                data: @json($chartJumlah),
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
