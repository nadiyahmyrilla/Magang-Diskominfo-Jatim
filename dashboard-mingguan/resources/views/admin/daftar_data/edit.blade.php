@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Data Daftar Data</h3>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validasi Gagal!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.daftar_data.update', $daftarData->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Perangkat Daerah <span class="text-danger">*</span></label>
            <input type="text" name="perangkat_daerah" class="form-control @error('perangkat_daerah') is-invalid @enderror" 
                   value="{{ old('perangkat_daerah', $daftarData->perangkat_daerah) }}" required>
            @error('perangkat_daerah')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    

        <div class="mb-3">
            <label>Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', $daftarData->jumlah) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('jumlah')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Tanggal Target <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_target" class="form-control @error('tanggal_target') is-invalid @enderror" 
                   value="{{ old('tanggal_target', $daftarData->tanggal_target->format('Y-m-d')) }}" required>
            @error('tanggal_target')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('admin.daftar_data.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
