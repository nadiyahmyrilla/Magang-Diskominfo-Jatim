













@extends('layouts.user')

@section('content')

<div class="dashboard-top mb-4">
    <div class="stat-grid">
        <div class="stat-card">
            <h6>JUMLAH AGENDA BULAN INI</h6>
            <h2>{{ $jumlahAgenda }}</h2>
            <span>{{ $tanggalTerbaru?->format('Y-m-d') ?? '-' }}</span>
        </div>
        <div class="stat-card">
            <h6>RATA-RATA PROGRESS</h6>
            <h2>{{ round($rataProgress, 2) }}%</h2>
            <span>{{ $tanggalTerbaru?->format('Y-m-d') ?? '-' }}</span>
        </div>
        <div class="stat-card">
            <h6>TOTAL PROGRESS</h6>
            <h2>{{ round($totalProgress, 2) }}%</h2>
            <span>{{ $tanggalTerbaru?->format('Y-m-d') ?? '-' }}</span>
        </div>
        <div class="stat-card">
            <h6>STATUS</h6>
            <h2>{{ $rataProgress >= 75 ? 'Baik' : ($rataProgress >= 50 ? 'Cukup' : 'Perlu Ditingkatkan') }}</h2>
            <span>{{ $tanggalTerbaru?->format('Y-m-d') ?? '-' }}</span>
        </div>
    </div>

    <div class="chart-card">
        <h5>Konten Tematik</h5>
        <span>{{ $tanggalTerbaru?->format('Y-m-d') ?? '-' }}</span>
        <canvas id="kontenChart" height="180"></canvas>
    </div>
</div>

<div class="table-section">
    <div class="table-header align-items-start">
        <h4 class="mb-0">Data Konten Tematik</h4>
        <div class="table-actions align-items-start">
            <form method="GET" action="{{ route('user.konten_tematik.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cari Data</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik agenda atau tanggal" value="{{ request('search') }}">
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
                    <button class="btn btn-outline-secondary w-100">Filter</button>
                    <a href="{{ route('user.konten_tematik.index') }}" class="btn btn-outline-danger w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrapper mt-3">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Agenda</th>
                    <th>Progress (%)</th>
                    <th>Data Dukung</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal_target)->format('Y-m-d') }}</td>
                    <td>{{ $row->agenda }}</td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $row->progress }}%;" 
                                 aria-valuenow="{{ $row->progress }}" aria-valuemin="0" aria-valuemax="100">
                                {{ round($row->progress, 2) }}%
                            </div>
                        </div>
                    </td>
                    <td>
                        @if ($row->data_dukung)
                            <span class="badge bg-info">{{ Str::limit($row->data_dukung, 30) }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Data tidak ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('kontenChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json(collect($chartLabels)->map(fn($d) => $d ? (Str::contains($d, 'T') ? Str::before($d, 'T') : $d) : null)),
        datasets: [
            {
                label: 'Progress Agenda',
                data: @json($chartProgress),
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(37, 99, 235, 0.1)'
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
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>

@endsection
