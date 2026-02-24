@extends('layouts.admin')

@section('content')

{{-- ================== SECTION ATAS ================== --}}
<div class="dashboard-top mb-4">

    <div class="stat-grid">
        <div class="stat-card">
            <h6>JUMLAH VIEW</h6>
            <h2>{{ $jumlahView }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH DOWNLOAD</h6>
            <h2>{{ $jumlahDownload }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH PERIODE</h6>
            <h2>{{ $jumlahPeriode }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="stat-card">
            <h6>JUMLAH TOTAL</h6>
            <h2>{{ $jumlahTotal }}</h2>
            <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        </div>
    </div>

    <div class="chart-card">
        <h5>Penggunaan Data</h5>
        <span>{{ $tanggalTerbaru ? \Carbon\Carbon::parse($tanggalTerbaru)->format('Y-m-d') : '-' }}</span>
        <canvas id="penggunaanChart" height="180"></canvas>
    </div>
</div>

{{-- ================== SECTION TABEL ================== --}}
<div class="table-section">

    <div class="table-header align-items-start">
        <h4 class="mb-0">Data Penggunaan Data</h4>

        <div class="table-actions align-items-start">
            <a href="{{ route('admin.penggunaan_data.create') }}" class="btn btn-primary tambah-data-btn">
                Tambah Data +
            </a>

            {{-- ================= FORM FILTER ================= --}}
            <form method="GET"
                  action="{{ route('admin.penggunaan_data.index') }}"
                  class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cari Data</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Ketik periode atau tanggal"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Periode Awal</label>
                    <input type="date"
                           name="tanggal_awal"
                           class="form-control"
                           value="{{ request('tanggal_awal') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Periode Akhir</label>
                    <input type="date"
                           name="tanggal_akhir"
                           class="form-control"
                           value="{{ request('tanggal_akhir') }}">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-secondary w-100">
                        Filter
                    </button>

                    <a href="{{ route('admin.penggunaan_data.index') }}"
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
                    <th>Periode</th>
                    <th>Tanggal</th>
                    <th>View</th>
                    <th>Download</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                <tr>
                    <td>{{ optional($row->periode_awal)->format('Y-m-d') }} - {{ optional($row->periode_akhir)->format('Y-m-d') }}</td>
                    <td>{{ optional($row->tanggal)->format('Y-m-d') }}</td>
                    <td>{{ $row->view }}</td>
                    <td>{{ $row->download }}</td>
                    <td>
                        <a href="{{ route('admin.penggunaan_data.edit', $row->id) }}"
                           class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('admin.penggunaan_data.destroy', $row->id) }}"
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
                    <td colspan="5" class="text-center">
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
const ctx = document.getElementById('penggunaanChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'View',
                data: @json($chartView),
                borderWidth: 2,
                tension: 0.4
            },
            {
                label: 'Download',
                data: @json($chartDownload),
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
