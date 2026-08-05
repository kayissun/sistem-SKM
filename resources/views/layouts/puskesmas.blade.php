<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Puskesmas') - SKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('puskesmas.dashboard') }}">
            SKM - {{ auth()->user()->puskesmas->nama ?? 'Puskesmas' }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('puskesmas.dashboard') }}">Dashboard</a></li>
                @role('admin-puskesmas')
                <li class="nav-item"><a class="nav-link" href="{{ route('puskesmas.petugas.index') }}">Petugas</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('puskesmas.pertanyaan.index') }}">Pertanyaan Survei</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('puskesmas.unit-layanan.index') }}">Unit Layanan</a></li>
                @endrole
                <li class="nav-item"><a class="nav-link" href="{{ route('puskesmas.laporan.index') }}">Laporan</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('survei.create', auth()->user()->puskesmas) }}" target="_blank">Link survei</a>
                </li>
                @role('dinkes')
                <li class="nav-item">
                    <a class="nav-link bg-dark bg-opacity-25 rounded" href="{{ route('dinkes.dashboard') }}">
                        &larr; Panel Pengawasan Dinkes
                    </a>
                </li>
                @endrole
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-light btn-sm" type="submit">Logout ({{ auth()->user()->name ?? '' }})</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@include('partials.skrip-salin-tabel')
</body>
</html>
