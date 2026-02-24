@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Tambah Data Penggunaan Data</h3>

    <form action="{{ route('admin.penggunaan_data.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Periode Awal</label>
            <input type="date" name="periode_awal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Periode Akhir</label>
            <input type="date" name="periode_akhir" class="form-control" required>
        </div>

        {{-- `tanggal` diisi otomatis (hari ini) dan tidak bisa diubah oleh user --}}

            

        <div class="mb-3">
            <label>View</label>
            <input type="number" name="view" class="form-control @error('view') is-invalid @enderror" placeholder="Jumlah view" value="{{ old('view') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('view')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Download</label>
            <input type="number" name="download" class="form-control @error('download') is-invalid @enderror" placeholder="Jumlah download" value="{{ old('download') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('download')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.penggunaan_data.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
