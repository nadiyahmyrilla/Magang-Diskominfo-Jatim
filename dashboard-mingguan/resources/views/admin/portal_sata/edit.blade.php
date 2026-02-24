@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Portal SATA</h3>

    <form action="{{ route('admin.portal_sata.update', $row->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tanggal Target</label>
            <input type="date" name="tanggal_target" class="form-control" value="{{ old('tanggal_target', optional($row->tanggal_target)->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label>Periode</label>
            <input type="text" name="nama_dataset" class="form-control" value="{{ old('nama_dataset', $row->nama_dataset) }}" required>
        </div>

            

        <div class="mb-3">
            <label>Dataset</label>
            <input type="number" name="dataset" class="form-control @error('dataset') is-invalid @enderror" value="{{ old('dataset', $row->dataset) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('dataset')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Target Total</label>
            <input type="number" name="target_total" class="form-control @error('target_total') is-invalid @enderror" value="{{ old('target_total', $row->target_total) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('target_total')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Capaian</label>
            <input type="number" name="capaian" class="form-control @error('capaian') is-invalid @enderror" value="{{ old('capaian', $row->capaian) }}" min="0" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @error('capaian')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Persentase Capaian (%)</label>
            <input type="text" name="capaian(%)" class="form-control" value="{{ old('capaian(%)', $row->{'capaian(%)'}) }}" readonly id="persentase-capaian">
        </div>

        <script>
        function updatePersentase() {
            const capaian = parseFloat(document.querySelector('[name="capaian"]').value) || 0;
            const total = parseFloat(document.querySelector('[name="target_total"]').value) || 0;
            let persen = 0;
            if (total > 0) persen = (capaian / total) * 100;
            document.getElementById('persentase-capaian').value = persen.toFixed(2);
        }
        document.querySelector('[name="capaian"]').addEventListener('input', updatePersentase);
        document.querySelector('[name="target_total"]').addEventListener('input', updatePersentase);
        updatePersentase();
        </script>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.portal_sata.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
