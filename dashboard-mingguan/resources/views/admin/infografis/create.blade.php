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
            <input type="text" name="sosial" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Ekonomi</label>
            <input type="text" name="ekonomi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Pertanian</label>
            <input type="text" name="pertanian" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Link Bukti</label>
            <input type="text" name="link_bukti" class="form-control">
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.infografis.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
