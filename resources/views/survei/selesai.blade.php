@extends('layouts.publik')

@section('title', 'Terima kasih')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-5 text-center">
            <h4 class="mb-2">Terima kasih!</h4>
            <p class="text-muted">Penilaian Anda untuk {{ $puskesmas->nama }} sudah berhasil direkam dan akan membantu kami meningkatkan kualitas pelayanan.</p>
            <a href="{{ route('survei.create', $puskesmas) }}" class="btn btn-outline-primary mt-2">Isi survei lagi</a>
        </div>
    </div>
@endsection
