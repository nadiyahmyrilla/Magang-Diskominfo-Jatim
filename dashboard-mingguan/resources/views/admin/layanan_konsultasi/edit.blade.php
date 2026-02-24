@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Data Layanan Konsultasi</h3>

    <form action="{{ route('admin.layanan_konsultasi.update', $layananKonsultasi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tanggal Pelaksanaan</label>
            <input type="date" name="tanggal_target" class="form-control"
                   value="{{ old('tanggal_target', $layananKonsultasi->tanggal_target) }}" required>
        </div>

        <div class="mb-3">
            <label>Perangkat Daerah</label>
            <input type="text" name="perangkat_daerah" class="form-control"
                   value="{{ old('perangkat_daerah', $layananKonsultasi->perangkat_daerah) }}" required>
        </div>


        <div class="mb-3">
            <label>Laki-laki</label>
            <input type="number" name="laki_laki" class="form-control @error('laki_laki') is-invalid @enderror"
                value="{{ old('laki_laki', $layananKonsultasi->laki_laki) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('laki_laki')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Perempuan</label>
            <input type="number" name="perempuan" class="form-control @error('perempuan') is-invalid @enderror"
                value="{{ old('perempuan', $layananKonsultasi->perempuan) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('perempuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.layanan_konsultasi.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
