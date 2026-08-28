@extends('layouts.puskesmas')

@section('title', 'Jawaban Teks')

@section('content')
    <style>
        .sp-table-card { border-radius: 14px; overflow: hidden; border: 1px solid #E4DEF7; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card tbody tr:hover { background: #FAF8FF; }
        .sp-table-card td, .sp-table-card th { border-bottom: 1px solid #E4DEF7; border-right: 1px solid #E4DEF7; }
        .sp-table-card td:last-child, .sp-table-card th:last-child { border-right: none; }
        .sp-table-card thead th { border-bottom: 2px solid #E4DEF7; background: #FAF8FF; }
        .sp-table-card tbody tr:last-child td { border-bottom: none; }
    </style>

    <div class="mb-3">
        <a href="{{ route('puskesmas.laporan.index', ['periode_survei_id' => $periode->id]) }}" class="btn btn-sm btn-light border text-secondary rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Laporan
        </a>
    </div>

    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color:#180733">{{ $pertanyaan->teks_pertanyaan }}</h3>
        <p class="text-muted small mb-0">{{ $puskesmas->nama }} &middot; Periode: {{ $periode->nama }} &middot; Total <strong>{{ $daftarJawaban->total() }}</strong> masukan</p>
    </div>

    <div class="card sp-table-card border-0 shadow-sm mb-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th style="width:160px">Tanggal</th>
                        <th style="width:200px">Nama Responden</th>
                        <th style="width:140px">No. HP</th>
                        <th>Isi Masukan / Saran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarJawaban as $jawaban)
                        <tr>
                            <td class="text-secondary small">{{ $jawaban->created_at->format('d M Y H:i') }}</td>
                            <td class="fw-semibold" style="color:#180733">{{ $jawaban->surveiJawaban->nama }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $jawaban->surveiJawaban->no_hp }}</span></td>
                            <td class="text-dark">{{ $jawaban->jawaban_teks }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada masukan teks pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        {{ $daftarJawaban->links('pagination::bootstrap-5') }}
    </div>
@endsection
