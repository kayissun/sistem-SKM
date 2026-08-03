@extends('layouts.puskesmas')

@section('title', 'Jawaban Teks')

@section('content')
    <a href="{{ route('puskesmas.laporan.index', ['periode_survei_id' => $periode->id]) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Kembali ke Laporan</a>

    <h3 class="mb-1">{{ $pertanyaan->teks_pertanyaan }}</h3>
    <p class="text-muted">{{ $puskesmas->nama }} &middot; Periode: {{ $periode->nama }} &middot; {{ $daftarJawaban->total() }} masukan</p>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th style="width:150px">Tanggal</th>
                <th style="width:200px">Nama</th>
                <th style="width:150px">No. HP</th>
                <th>Isi Masukan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarJawaban as $jawaban)
                <tr>
                    <td>{{ $jawaban->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $jawaban->surveiJawaban->nama }}</td>
                    <td>{{ $jawaban->surveiJawaban->no_hp }}</td>
                    <td>{{ $jawaban->jawaban_teks }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada masukan pada periode ini</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $daftarJawaban->links() }}
@endsection
