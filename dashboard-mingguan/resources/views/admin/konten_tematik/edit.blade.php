@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Data Konten Tematik</h3>

    <form action="{{ route('admin.konten_tematik.update', $kontenTematik->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tanggal Target</label>
            <input type="date" name="tanggal_target" class="form-control"
                   value="{{ old('tanggal_target', $kontenTematik->tanggal_target) }}" required>
        </div>

        <div class="mb-3">
            <label>Agenda</label>
            <input type="text" name="agenda" class="form-control"
                   value="{{ old('agenda', $kontenTematik->agenda) }}" required>
        </div>

        <div class="mb-3">
            <label>Progress (%)</label>
            <input type="number" name="progress" class="form-control" min="0" max="100" step="0.01"
                   value="{{ old('progress', $kontenTematik->progress) }}" required>
        </div>

        <div class="mb-3">
            <label>Data Dukung (Link atau File)</label>
            <input type="text" name="data_dukung" class="form-control"
                   value="{{ old('data_dukung', $kontenTematik->data_dukung) }}">
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.konten_tematik.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
