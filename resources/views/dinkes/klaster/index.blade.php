@extends('layouts.dinkes')

@section('title', 'Klaster Performa Unit')

@section('content')
    <h3 class="mb-1">Klaster Performa Unit</h3>
    <p class="text-muted">
        Pengelompokan Puskesmas/RSU berdasarkan kemiripan pola nilai 9 unsur pelayanan
        (bukan cuma dari 1 angka rata-rata) — pakai algoritma K-Means.
    </p>

    <form method="GET" class="row g-2 mb-4" style="max-width:400px">
        <div class="col-8">
            <select name="periode_survei_id" class="form-select" onchange="this.form.submit()">
                @foreach ($daftarPeriode as $p)
                    <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                        {{ $p->nama }} @if($p->is_active) (aktif) @endif
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if (!$periode)
        <div class="alert alert-warning">Belum ada periode survei. Buat periode terlebih dahulu.</div>
    @elseif ($kelompok->isEmpty())
        <div class="alert alert-warning">
            Belum cukup data untuk dikelompokkan pada periode ini — minimal butuh 1 unit dengan
            responden dan semua 9 unsur wajib sudah dipetakan ke pertanyaan.
        </div>
    @else
        @foreach ($kelompok as $k)
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                        <div>
                            <h5 class="mb-1">{{ $k['label'] }}</h5>
                            <span class="text-muted small">
                                {{ $k['anggota']->count() }} unit &middot; rata-rata nilai SKM {{ $k['rata_rata_skor'] }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-4 align-items-center">
                        <div class="col-md-4 text-center">
                            @include('partials.radar-unsur', ['nilai' => $k['centroid']])
                            <div class="text-muted small mt-1">Profil rata-rata kelompok ini</div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered table-sm bg-white mb-0">
                                <thead>
                                    <tr>
                                        <th>Unit</th>
                                        <th style="width:120px">Nilai SKM</th>
                                        <th style="width:140px">Mutu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($k['anggota'] as $anggota)
                                        <tr>
                                            <td>{{ $anggota['nama'] }}</td>
                                            <td>{{ $anggota['nilai_akhir_skm'] }}</td>
                                            <td>{{ $anggota['mutu_akhir'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($dikecualikan->isNotEmpty())
            <div class="alert alert-secondary">
                <strong>Tidak ikut dikelompokkan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($dikecualikan as $item)
                        <li>{{ $item['nama'] }} — {{ $item['alasan'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
@endsection
