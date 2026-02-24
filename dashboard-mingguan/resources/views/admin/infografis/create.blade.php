@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Tambah Data Infografis</h3>

    <form action="{{ route('admin.infografis.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Tanggal Target</label>
            <input type="date" name="tanggal_target" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Periode (Mingguan)</label>
            <input type="text" name="periode" class="form-control" placeholder="02 – 08 Februari 2026" required>
        </div>

        <div class="mb-3">
            <label>Sosial</label>
            <input type="number" name="sosial" class="form-control @error('sosial') is-invalid @enderror" value="{{ old('sosial') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('sosial')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Ekonomi</label>
            <input type="number" name="ekonomi" class="form-control @error('ekonomi') is-invalid @enderror" value="{{ old('ekonomi') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('ekonomi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Pertanian</label>
            <input type="number" name="pertanian" class="form-control @error('pertanian') is-invalid @enderror" value="{{ old('pertanian') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('pertanian')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Link Bukti</label>
            <input type="text" name="link_bukti" class="form-control @error('link_bukti') is-invalid @enderror" value="{{ old('link_bukti') }}">
            @error('link_bukti')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.infografis.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
