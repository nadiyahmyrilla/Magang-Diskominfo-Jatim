













@extends('layouts.user')

@section('content')

{{-- ================== SECTION ATAS ================== --}}
<div class="dashboard-top mb-4">
    <div class="stat-grid">
        <div class="stat-card">
            <h6>JUMLAH SOSIAL</h6>
            <h2>{{ $jumlahSosial }}</h2>
            <span>{{ $tanggalTerbaru }}</span>
        </div>
        <div class="stat-card">
            <h6>JUMLAH EKONOMI</h6>
            <h2>{{ $jumlahEkonomi }}</h2>
            <span>{{ $tanggalTerbaru }}</span>
        </div>
        <div class="stat-card">
            <h6>JUMLAH TOTAL PERIODE</h6>
            <h2>{{ $totalPeriode }}</h2>
            <span>{{ $tanggalTerbaru }}</span>
        </div>
        <div class="stat-card">
            <h6>JUMLAH PERTANIAN</h6>
            <h2>{{ $jumlahPertanian }}</h2>
            <span>{{ $tanggalTerbaru }}</span>
        </div>
    </div>

    <div class="chart-card">
        <h5>Infografis</h5>
        <span>{{ $tanggalTerbaru }}</span>
        <canvas id="infografisChart" height="180"></canvas>
    </div>
</div>

{{-- ================== SECTION TABEL ================== --}}
<div class="table-section">
    <div class="table-header align-items-start">
        <h4 class="mb-0">Infografis</h4>
        <div class="table-actions align-items-start">
            {{-- ================= FORM FILTER ================= --}}
            <form method="GET"
                  action="{{ route('user.infografis.index') }}"
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
                    <a href="{{ route('user.infografis.index') }}"
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
                    <th>Sosial</th>
                    <th>Ekonomi</th>
                    <th>Pertanian</th>
                    <th>Link Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                <tr>
                    <td>{{ $row->periode }}</td>
                    <td>{{ $row->tanggal_target }}</td>
                    <td>{{ $row->sosial }}</td>
                    <td>{{ $row->ekonomi }}</td>
                    <td>{{ $row->pertanian }}</td>
                    <td>
                        @if ($row->link_bukti)
                            <a href="{{ $row->link_bukti }}" target="_blank" class="badge bg-primary">Link</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
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
const ctx = document.getElementById('infografisChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'Sosial',
                data: @json($chartSosial),
                borderWidth: 2,
                tension: 0.4
            },
            {
                label: 'Ekonomi',
                data: @json($chartEkonomi),
                borderWidth: 2,
                tension: 0.4
            },
            {
                label: 'Pertanian',
                data: @json($chartPertanian),
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
