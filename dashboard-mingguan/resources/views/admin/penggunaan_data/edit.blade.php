@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Data Penggunaan Data</h3>

    <form action="{{ route('admin.penggunaan_data.update', $penggunaanData->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Periode Awal</label>
            <input type="date" name="periode_awal" class="form-control"
                   value="{{ old('periode_awal', optional($penggunaanData->periode_awal)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label>Periode Akhir</label>
            <input type="date" name="periode_akhir" class="form-control"
                   value="{{ old('periode_akhir', optional($penggunaanData->periode_akhir)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Masukkan Data</label>
            <input type="text" class="form-control" value="{{ optional($penggunaanData->tanggal)->format('Y-m-d') }}" readonly>
            <div class="form-text">Tanggal ini diisi otomatis saat data dibuat dan tidak dapat diubah.</div>
        </div>

        <div class="mb-3">
            <label>View</label>
            <input type="number" name="view" class="form-control @error('view') is-invalid @enderror" value="{{ old('view', $penggunaanData->view) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('view')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Download</label>
            <input type="number" name="download" class="form-control @error('download') is-invalid @enderror" value="{{ old('download', $penggunaanData->download) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('download')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.penggunaan_data.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
