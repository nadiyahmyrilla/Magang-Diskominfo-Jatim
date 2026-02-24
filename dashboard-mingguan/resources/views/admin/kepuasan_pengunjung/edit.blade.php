@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Data Kepuasan Pengunjung</h3>

    <form action="{{ route('admin.kepuasan_pengunjung.update', $kepuasanPengunjung->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tanggal Pelaporan</label>
            <input type="date" name="tanggal_target" class="form-control"
                   value="{{ old('tanggal_target', $kepuasanPengunjung->tanggal_target) }}" required>
        </div>

        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki - Laki" {{ old('jenis_kelamin', $kepuasanPengunjung->jenis_kelamin) == 'Laki - Laki' ? 'selected' : '' }}>Laki - Laki</option>
                <option value="Perempuan" {{ old('jenis_kelamin', $kepuasanPengunjung->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Sangat Puas</label>
            <input type="number" name="sangat_puas" class="form-control @error('sangat_puas') is-invalid @enderror" value="{{ old('sangat_puas', $kepuasanPengunjung->sangat_puas) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('sangat_puas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Puas</label>
            <input type="number" name="puas" class="form-control @error('puas') is-invalid @enderror" value="{{ old('puas', $kepuasanPengunjung->puas) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('puas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Tidak Puas</label>
            <input type="number" name="tidak_puas" class="form-control @error('tidak_puas') is-invalid @enderror" value="{{ old('tidak_puas', $kepuasanPengunjung->tidak_puas) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('tidak_puas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Sangat Tidak Puas</label>
            <input type="number" name="sangat_tidak_puas" class="form-control @error('sangat_tidak_puas') is-invalid @enderror" value="{{ old('sangat_tidak_puas', $kepuasanPengunjung->sangat_tidak_puas) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('sangat_tidak_puas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.kepuasan_pengunjung.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
