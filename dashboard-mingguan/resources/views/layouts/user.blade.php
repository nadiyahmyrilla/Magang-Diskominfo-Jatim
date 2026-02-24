<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Admin CSS --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<div class="container mt-4 px-3">

    {{-- ================= NAVBAR ================= --}}
    <div class="navbar-custom d-flex justify-content-between align-items-center mb-4 px-5">

        {{-- LEFT --}}
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" height="70" style="transform:none;">
        </div>

        {{-- CENTER --}}
        <nav class="top-navbar">
            <ul class="nav-menu">
                <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        <i class="icon home"></i>
                        <span>Dashboard Utama</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('user.layanan_konsultasi.index') ? 'active' : '' }}">
                    <a href="{{ route('user.layanan_konsultasi.index') }}">
                        <i class="icon edit"></i>
                        <span>Layanan Konsultasi</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('user.kepuasan_pengunjung.index') ? 'active' : '' }}">
                    <a href="{{ route('user.kepuasan_pengunjung.index') }}">
                        <i class="icon users"></i>
                        <span>Kepuasan Pengunjung</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('user.penggunaan_data.index') ? 'active' : '' }}">
                    <a href="{{ route('user.penggunaan_data.index') }}">
                        <i class="icon data"></i>
                        <span>Pengguna Data</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('user.daftar_data.index') ? 'active' : '' }}">
                    <a href="{{ route('user.daftar_data.index') }}">
                        <i class="icon list"></i>
                        <span>Daftar Data</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('user.infografis.index') ? 'active' : '' }}">
                    <a href="{{ route('user.infografis.index') }}">
                        <i class="icon info"></i>
                        <span>Infografis</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('user.portal_sata.index') ? 'active' : '' }}">
                    <a href="{{ route('user.portal_sata.index') }}">
                        <i class="icon monitor"></i>
                        <span>Portal SATA</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('user.konten_tematik.index') ? 'active' : '' }}">
                    <a href="{{ route('user.konten_tematik.index') }}">
                        <i class="icon book"></i>
                        <span>Konten Tematik</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('user.rekomendasi_statistik.index') ? 'active' : '' }}">
                    <a href="{{ route('user.rekomendasi_statistik.index') }}">
                        <i class="icon chart"></i>
                        <span>Rekomendasi Statistik</span>
                    </a>
                </li>
            </ul>
        </nav>

        {{-- RIGHT --}}
        <div class="d-flex align-items-center gap-3">
            <div>
                <img src="{{ asset('images/kominfo.png') }}" alt="Kominfo" height="80" width="90">
            </div>
        </div>

    </div>

    {{-- ================= CONTENT ================= --}}
    <div class="content-wrapper">
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
