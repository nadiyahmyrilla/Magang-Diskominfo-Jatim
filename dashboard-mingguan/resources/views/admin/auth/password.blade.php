@extends('layouts.admin')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h4 class="mb-3 text-center">Ubah Password Admin</h4>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            <div class="mb-3">
                <label for="current_password" class="form-label">Password Lama</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="current_password" name="current_password" required autofocus>
                    <button type="button" class="btn btn-outline-secondary" id="toggleCurrentPassword" tabindex="-1"><i id="iconCurrentPassword" class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button type="button" class="btn btn-outline-secondary" id="toggleNewPassword" tabindex="-1"><i id="iconNewPassword" class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword" tabindex="-1"><i id="iconConfirmPassword" class="bi bi-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ubah Password</button>
        </form>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <script>
            function setupToggle(inputId, btnId, iconId) {
                const input = document.getElementById(inputId);
                const btn = document.getElementById(btnId);
                const icon = document.getElementById(iconId);
                btn.addEventListener('click', function() {
                    const type = input.type === 'password' ? 'text' : 'password';
                    input.type = type;
                    icon.className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
                });
            }
            setupToggle('current_password', 'toggleCurrentPassword', 'iconCurrentPassword');
            setupToggle('password', 'toggleNewPassword', 'iconNewPassword');
            setupToggle('password_confirmation', 'toggleConfirmPassword', 'iconConfirmPassword');
        </script>
    </div>
</div>
@endsection
