@extends('layouts.dinkes')

@section('title', 'Klaster Performa Unit')

@section('content')
    <style>
        .cluster-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
        .cluster-head h3 { color:#180733; font-weight:800; margin:0 0 4px; }
        .cluster-head p { color:#635C7A; margin:0; font-size:.88rem; }
        .cluster-actions { display:flex; gap:8px; flex-wrap:wrap; }
        .cluster-insight { border-left:4px solid #C88719; background:#FFF9EA; }
        .cluster-insight strong { color:#43206F; }
        .cluster-trend { font-size:.72rem; line-height:1.8; }
        .cluster-trend span { display:inline-block; margin-right:4px; padding:1px 6px; border-radius:99px; background:#F1EDFA; color:#4C1D95; }
        .cluster-table th { white-space:nowrap; }
    </style>

    <div class="cluster-head">
        <div>
            <h3>Klaster Performa Unit</h3>
            <p>Kelompok unit berdasarkan kemiripan pola nilai unsur pelayanan dengan algoritma K-Means.</p>
        </div>
        @if ($periode)
            <div class="cluster-actions">
                <a href="{{ route('dinkes.klaster.export-pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger">
                    <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('dinkes.klaster.export-excel', request()->query()) }}" class="btn btn-sm btn-outline-success">
                    <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                </a>
                <button type="button" class="btn btn-sm btn-light border" onclick="salinTabelKeClipboard('tabel-klaster-ringkas', this)">
                    <i class="fa-solid fa-copy me-1"></i> Salin Tabel
                </button>
            </div>
        @endif
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small text-muted fw-semibold">Periode Survei</label>
                <select name="periode_survei_id" class="form-select">
                @foreach ($daftarPeriode as $p)
                    <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                        {{ $p->nama }} @if($p->is_active) (aktif) @endif
                    </option>
                @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Jumlah kelompok (K)</label>
                <select name="jumlah_klaster" class="form-select">
                    @for ($k = 2; $k <= 6; $k++)
                        <option value="{{ $k }}" @selected(($jumlahKlaster ?: 4) === $k)>{{ $k }} kelompok</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Terapkan</button>
            </div>
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
        @if ($peringatanKualitas)
            <div class="alert alert-warning border-0 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $peringatanKualitas }}
            </div>
        @endif

        <div class="d-flex gap-3 flex-wrap mb-3 small text-muted">
            <span><strong>{{ $jumlahSampel }}</strong> unit dianalisis</span>
            <span><strong>{{ $jumlahKlaster }}</strong> kelompok terbentuk</span>
            <span>Periode: <strong>{{ $periode->nama }}</strong></span>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>Insight Otomatis</div>
            <div class="card-body">
                @foreach ($insight as $item)
                    <div class="cluster-insight rounded-3 p-3 mb-2">
                        <strong>{{ $item['cluster'] }}</strong>
                        <span class="text-muted ms-1">(rata-rata {{ $item['rata_rata'] }})</span>
                        <div>{{ $item['kesimpulan'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-table me-2"></i>Ringkasan Unit</span>
                <span class="small text-muted">Gunakan Salin Tabel untuk rapat</span>
            </div>
            <div class="table-responsive">
                <table id="tabel-klaster-ringkas" class="table table-sm table-hover mb-0 cluster-table">
                    <thead><tr><th>Unit</th><th>Kelompok</th><th>Nilai SKM</th><th>Mutu</th><th>Tren Periode</th></tr></thead>
                    <tbody>
                        @foreach ($kelompok as $k)
                            @foreach ($k['anggota'] as $anggota)
                                <tr>
                                    <td><a href="{{ route('dinkes.laporan.detail', ['puskesma' => $anggota['id'], 'periode_survei_id' => $periode->id]) }}" class="fw-semibold">{{ $anggota['nama'] }}</a></td>
                                    <td>{{ $k['label'] }}</td>
                                    <td>{{ $anggota['nilai_akhir'] }}</td>
                                    <td>{{ $anggota['mutu'] }}</td>
                                    <td class="cluster-trend">
                                        @forelse ($anggota['tren'] as $tren)
                                            <span title="{{ $tren['periode'] }}">{{ $tren['cluster'] }} · {{ $tren['nilai'] }}</span>
                                        @empty
                                            <span class="text-muted">Belum ada histori</span>
                                        @endforelse
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

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
                                        <th>Unit / Laporan</th>
                                        <th style="width:120px">Nilai SKM</th>
                                        <th style="width:140px">Mutu</th>
                                        <th>Tren</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($k['anggota'] as $anggota)
                                        <tr>
                                            <td><a href="{{ route('dinkes.laporan.detail', ['puskesma' => $anggota['id'], 'periode_survei_id' => $periode->id]) }}">{{ $anggota['nama'] }} <i class="fa-solid fa-arrow-up-right-from-square ms-1 small"></i></a></td>
                                            <td>{{ $anggota['nilai_akhir'] }}</td>
                                            <td>{{ $anggota['mutu'] }}</td>
                                            <td class="cluster-trend">
                                                @forelse ($anggota['tren'] as $tren)
                                                    <span title="{{ $tren['periode'] }}">{{ $tren['cluster'] }} · {{ $tren['nilai'] }}</span>
                                                @empty
                                                    <span class="text-muted">-</span>
                                                @endforelse
                                            </td>
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
