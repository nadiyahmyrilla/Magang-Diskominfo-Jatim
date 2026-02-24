@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Tambah Rekomendasi Statistik</h3>

    <form action="{{ route('admin.rekomendasi_statistik.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Tanggal Target</label>
            <input type="date" name="tanggal_target" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Instansi Tujuan</label>
            <input type="text" name="instansi_tujuan" class="form-control" required>
        </div>


        <div class="mb-3">
            <label>Batal</label>
            <input autocomplete="off" type="number" name="Batal" id="input-batal" class="form-control @error('Batal') is-invalid @enderror" value="{{ old('Batal') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('Batal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Layak</label>
            <input autocomplete="off" type="number" name="Layak" id="input-layak" class="form-control @error('Layak') is-invalid @enderror" value="{{ old('Layak') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('Layak')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Pemeriksaan</label>
            <input autocomplete="off" type="number" name="Pemeriksaan" id="input-pemeriksaan" class="form-control @error('Pemeriksaan') is-invalid @enderror" value="{{ old('Pemeriksaan') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('Pemeriksaan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Pengajuan</label>
            <input autocomplete="off" type="number" name="Pengajuan" id="input-pengajuan" class="form-control @error('Pengajuan') is-invalid @enderror" value="{{ old('Pengajuan') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('Pengajuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Pengesahan</label>
            <input autocomplete="off" type="number" name="Pengesahan" id="input-pengesahan" class="form-control @error('Pengesahan') is-invalid @enderror" value="{{ old('Pengesahan') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('Pengesahan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Perbaikan</label>
            <input autocomplete="off" type="number" name="Perbaikan" id="input-perbaikan" class="form-control @error('Perbaikan') is-invalid @enderror" value="{{ old('Perbaikan') }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('Perbaikan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Total</label>
            <input type="text" name="Total" class="form-control" value="" readonly id="total-field">
        </div>

        <script>
        function updateTotal() {
            const fields = ['input-batal','input-layak','input-pemeriksaan','input-pengajuan','input-pengesahan','input-perbaikan'];
            let total = 0;
            fields.forEach(f => {
                const el = document.getElementById(f);
                if (el) total += parseInt(el.value || 0);
            });
            document.getElementById('total-field').value = total;
        }
        ['input-batal','input-layak','input-pemeriksaan','input-pengajuan','input-pengesahan','input-perbaikan'].forEach(f => {
            const el = document.getElementById(f);
            if (el) el.addEventListener('input', updateTotal);
        });
        </script>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.rekomendasi_statistik.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
