@extends('layouts.publik')

@section('title', 'Terlalu Banyak Percobaan')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-5 text-center">
            <div class="mb-3" style="font-size:3rem">⏳</div>
            <h4 class="mb-2">Mohon Tunggu Sebentar</h4>
            <p class="text-muted mb-1">
                Untuk menjaga kualitas data survei, ada batas jumlah pengisian dalam waktu singkat
                dari perangkat yang sama.
            </p>

            @php
                $retryAfter = method_exists($exception ?? null, 'getHeaders')
                    ? ($exception->getHeaders()['Retry-After'] ?? null)
                    : null;
            @endphp

            @if ($retryAfter)
                <p class="text-muted">
                    Silakan coba lagi dalam kurang lebih <strong>{{ max(1, ceil($retryAfter / 60)) }} menit</strong>.
                </p>
            @else
                <p class="text-muted">Silakan coba lagi dalam beberapa menit.</p>
            @endif

            <a href="javascript:history.back()" class="btn btn-outline-primary mt-2">Kembali ke Form</a>
        </div>
    </div>
@endsection
