<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
            <img src="{{ asset('images/logo.png') }}" alt="Logo" height="60" style="transform:none;">
        </div>

        {{-- CENTER --}}
        <nav class="top-navbar">
            <ul class="nav-menu">
                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="icon home"></i>
                        <span>Dashboard Utama</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.layanan_konsultasi.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.layanan_konsultasi.index') }}">
                        <i class="icon edit"></i>
                        <span>Layanan Konsultasi</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.kepuasan_pengunjung.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.kepuasan_pengunjung.index') }}">
                        <i class="icon users"></i>
                        <span>Kepuasan Pengunjung</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.penggunaan_data.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.penggunaan_data.index') }}">
                        <i class="icon data"></i>
                        <span>Pengguna Data</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.daftar_data.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.daftar_data.index') }}">
                        <i class="icon list"></i>
                        <span>Daftar Data</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.infografis.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.infografis.index') }}">
                        <i class="icon info"></i>
                        <span>Infografis</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.portal_sata.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.portal_sata.index') }}">
                        <i class="icon monitor"></i>
                        <span>Portal SATA</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.konten_tematik.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.konten_tematik.index') }}">
                        <i class="icon book"></i>
                        <span>Konten Tematik</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.rekomendasi_statistik.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.rekomendasi_statistik.index') }}">
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
            
            {{-- Logout Button (User/Admin Info) --}}
            <div class="dropdown" style="margin-left: 20px;">
                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-person-circle"></i>
                    <span>{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.password.edit') }}">
                            <i class="bi bi-key"></i> Ubah Password
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item" onclick="return confirm('Anda yakin ingin keluar?')">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
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
