@extends('layouts.admin')

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

    <div class="table-header">
        <h4>Pengguna Data</h4>

        <div class="table-actions d-flex gap-2">
            <a href="{{ route('admin.infografis.create') }}" class="btn btn-primary">
                Tambah data +
            </a>

            <form method="GET" action="{{ route('admin.infografis.index') }}" class="d-flex gap-2">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Cari periode / tanggal"
                       value="{{ request('search') }}">

                <select name="minggu" class="form-select">
                    <option value="">Semua Minggu</option>
                    @for ($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" {{ request('minggu') == $i ? 'selected' : '' }}>
                            Minggu {{ $i }}
                        </option>
                    @endfor
                </select>

                <button class="btn btn-outline-secondary">Cari</button>
            </form>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Tanggal</th>
                    <th>Sosial</th>
                    <th>Ekonomi</th>
                    <th>Pertanian</th>
                    <th>Link Bukti</th>
                    <th>Aksi</th>
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
                            <a href="{{ $row->link_bukti }}" target="_blank">Link</a>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.infografis.edit', $row->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.infografis.destroy', $row->id) }}" method="POST" class="d-inline">
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
                    <td colspan="7" class="text-center">Data tidak ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $data->links() }}
    </div>
</div>

@endsection
