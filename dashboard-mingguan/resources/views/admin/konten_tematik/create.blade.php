@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Tambah Data Konten Tematik</h3>

    <form action="{{ route('admin.konten_tematik.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Tanggal Target</label>
            <input type="date" name="tanggal_target" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Agenda</label>
            <input type="text" name="agenda" class="form-control" placeholder="Masukkan agenda konten tematik" required>
        </div>

        <div class="mb-3">
            <label>Progress (%)</label>
            <input type="number" name="progress" class="form-control" min="0" max="100" step="0.01" placeholder="0-100" required>
        </div>

        <div class="mb-3">
            <label>Data Dukung (Link atau File)</label>
            <input type="text" name="data_dukung" class="form-control" placeholder="https://... atau nama file">
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.konten_tematik.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
