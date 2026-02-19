@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Data Infografis</h3>

    <form action="{{ route('admin.infografis.update', $infografis->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tanggal Target</label>
            <input type="date" name="tanggal_target" class="form-control"
                   value="{{ old('tanggal_target', $infografis->tanggal_target) }}" required>
        </div>

        <div class="mb-3">
            <label>Periode (Mingguan)</label>
            <input type="text" name="periode" class="form-control"
                   value="{{ old('periode', $infografis->periode) }}" required>
        </div>

        <div class="mb-3">
            <label>Sosial</label>
            <input type="text" name="sosial" class="form-control"
                   value="{{ old('sosial', $infografis->sosial) }}" required>
        </div>

        <div class="mb-3">
            <label>Ekonomi</label>
            <input type="text" name="ekonomi" class="form-control"
                   value="{{ old('ekonomi', $infografis->ekonomi) }}" required>
        </div>

        <div class="mb-3">
            <label>Pertanian</label>
            <input type="text" name="pertanian" class="form-control"
                   value="{{ old('pertanian', $infografis->pertanian) }}" required>
        </div>

        <div class="mb-3">
            <label>Link Bukti</label>
            <input type="text" name="link_bukti" class="form-control"
                   value="{{ old('link_bukti', $infografis->link_bukti) }}">
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.infografis.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
