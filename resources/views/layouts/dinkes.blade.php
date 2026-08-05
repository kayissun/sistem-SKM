<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dinas Kesehatan') - SKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dinkes.dashboard') }}">SKM - Dinas Kesehatan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('dinkes.puskesmas.index') }}">Puskesmas / RSU</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('dinkes.unsur-pelayanan.index') }}">Unsur Pelayanan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('dinkes.periode-survei.index') }}">Periode Survei</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('dinkes.laporan.index') }}">Laporan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('dinkes.klaster.index') }}">Klaster Performa</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('dinkes.aktivitas.index') }}">Log Aktivitas</a></li>
                <li class="nav-item">
                    <a class="nav-link bg-primary bg-opacity-25 rounded" href="{{ route('puskesmas.dashboard') }}">
                        SKM Dinkes Sendiri
                    </a>
                </li>
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
