@extends('layouts.dinkes')

@section('title', 'Klaster Performa Unit')

@section('content')
    <style>
        .cluster-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
        .cluster-head h3 { color:#180733; font-weight:800; margin:0 0 4px; }
        .cluster-head p { color:#635C7A; margin:0; font-size:.88rem; }
        .cluster-actions { display:flex; gap:8px; flex-wrap:wrap; }
        .cluster-trend { font-size:.72rem; line-height:1.9; text-align:right; }
        .cluster-trend span { display:inline-block; margin-left:4px; padding:1px 8px; border-radius:99px; background:#F1EDFA; color:#4C1D95; white-space:nowrap; }
        .cluster-trend span.aktif { background:#4C1D95; color:#fff; font-weight:700; }
        .cluster-table th { white-space:nowrap; }
        .rank-badge { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; font-weight:700; font-size:.78rem; }
        .rank-1 { background:#FEF3C7; color:#92400E; }
        .rank-2 { background:#E5E7EB; color:#374151; }
        .rank-3 { background:#FED7AA; color:#9A3412; }
        .rank-default { background:#F3F4F6; color:#6B7280; }
        .filter-btn { padding:4px 14px; border-radius:20px; font-size:.8rem; font-weight:600; border:1.5px solid #D1D5DB; background:#fff; color:#6B7280; cursor:pointer; transition:all .15s; }
        .filter-btn.active { background:#4C1D95; color:#fff; border-color:#4C1D95; }
        .filter-btn:hover:not(.active) { border-color:#4C1D95; color:#4C1D95; }
        .unsur-header { background:#FEF3C7; }
        .cluster-insight {
            background:#F3EFFA;
            border-left:3px solid #6D28D9;
            border-radius:8px;
            padding:8px 12px;
            font-size:.82rem;
            color:#43206F;
        }
        .cluster-member { border-top:1px solid #F1EDFA; padding:8px 0; }
        .cluster-member:first-child { border-top:none; }
        .cluster-label-badge { font-size:.72rem; font-weight:700; letter-spacing:.03em; }
    </style>

    <div class="cluster-head">
        <div>
            <h3>Klaster Performa Faskes</h3>
            <p>Pantau Klaster performa faskes.</p>
        </div>
        @if ($periode)
            <div class="cluster-actions">
                <a href="{{ route('dinkes.klaster.export-pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger">
                    <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('dinkes.klaster.export-excel', request()->query()) }}" class="btn btn-sm btn-outline-success">
                    <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                </a>
                <button type="button" class="btn btn-sm btn-light border" onclick="salinTabelKeClipboard('tabel-ranking-faskes', this)">
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

        {{-- ============================================================ --}}
        {{-- KARTU KELOMPOK KLASTER (radar + insight + anggota + tren)  --}}
        {{-- ============================================================ --}}
        <div class="row g-3 mb-4">
            @foreach ($kelompok as $kel)
                @php $insightKelompok = $insight->firstWhere('cluster', $kel['label']); @endphp
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span>
                                <span class="badge rounded-pill bg-primary cluster-label-badge">{{ $kel['label'] }}</span>
                                <span class="small text-muted ms-1">{{ $kel['anggota']->count() }} unit</span>
                            </span>
                            <span class="small text-muted">Rata-rata SKM <strong>{{ $kel['rata_rata_skor'] }}</strong></span>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-2">
                                @include('partials.radar-unsur', ['nilai' => $kel['centroid']])
                            </div>

                            @if ($insightKelompok)
                                <div class="cluster-insight mb-3">
                                    <i class="fa-solid fa-lightbulb me-1"></i> {{ $insightKelompok['kesimpulan'] }}
                                </div>
                            @endif

                            <div>
                                @foreach ($kel['anggota'] as $anggota)
                                    <div class="cluster-member d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                        <div>
                                            <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $anggota['id'], 'periode_survei_id' => $periode->id]) }}" class="fw-semibold text-decoration-none" style="font-size:.86rem">
                                                {{ $anggota['nama'] }}
                                            </a>
                                            <div class="text-muted" style="font-size:.74rem">SKM {{ $anggota['nilai_akhir'] }} &middot; {{ $anggota['mutu'] }}</div>
                                        </div>
                                        @if ($anggota['tren']->isNotEmpty())
                                            <div class="cluster-trend">
                                                @foreach ($anggota['tren'] as $tren)
                                                    <span class="{{ $tren['aktif'] ? 'aktif' : '' }}" title="Nilai SKM {{ $tren['nilai'] }}">
                                                        {{ $tren['periode'] }}: {{ $tren['cluster'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ============================================================ --}}
        {{-- TABEL 1: RANKING FASKES BERDASARKAN NILAI SKM              --}}
        {{-- ============================================================ --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span><i class="fa-solid fa-ranking-star me-2"></i>Ranking Faskes</span>
                <div class="d-flex gap-2" id="filterRanking">
                    <button type="button" class="filter-btn active" data-sort="skm-desc"><i class="fa-solid fa-ranking-star me-1"></i> Peringkat</button>
                    <button type="button" class="filter-btn" data-sort="nama"><i class="fa-solid fa-arrow-down-a-z me-1"></i> Nama</button>
                    <button type="button" class="filter-btn" data-sort="skm-asc"><i class="fa-solid fa-triangle-exclamation me-1"></i> Terburuk</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="tabel-ranking-faskes" class="table table-sm table-hover mb-0 cluster-table">
                    <thead>
                        <tr>
                            <th style="width:50px">Rank</th>
                            <th>Nama Faskes</th>
                            <th>Nilai SKM</th>
                            <th>Mutu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($semuaAnggota as $anggota)
                            <tr data-skm="{{ $anggota['nilai_akhir'] }}" data-nama="{{ mb_strtolower($anggota['nama']) }}" data-rank="{{ $anggota['peringkat'] }}">
                                <td><span class="rank-badge {{ $anggota['peringkat'] <= 3 ? 'rank-' . $anggota['peringkat'] : 'rank-default' }}">{{ $anggota['peringkat'] }}</span></td>
                                <td>
                                    <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $anggota['id'], 'periode_survei_id' => $periode->id]) }}" class="fw-semibold text-decoration-none">
                                        {{ $anggota['nama'] }}
                                    </a>
                                </td>
                                <td><strong>{{ $anggota['nilai_akhir'] }}</strong></td>
                                <td>{{ $anggota['mutu'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TABEL 2: NILAI UNSUR PER FASKES (U1 - U9)                  --}}
        {{-- ============================================================ --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <i class="fa-solid fa-layer-group me-2"></i>Nilai Unsur Pelayanan Per Faskes
                    <div class="small text-muted mt-1">Nilai NRR (skala 100) per unsur pelayanan untuk seluruh faskes.</div>
                </div>
                <div class="d-flex gap-2" id="filterUnsur">
                    <button type="button" class="filter-btn active" data-sort="skm-desc"><i class="fa-solid fa-ranking-star me-1"></i> Peringkat</button>
                    <button type="button" class="filter-btn" data-sort="nama"><i class="fa-solid fa-arrow-down-a-z me-1"></i> Nama</button>
                    <button type="button" class="filter-btn" data-sort="skm-asc"><i class="fa-solid fa-triangle-exclamation me-1"></i> Terburuk</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="tabel-unsur-faskes" class="table table-bordered table-sm text-center align-middle bg-white mb-0" style="font-size:.8rem">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle" style="width:50px">Rank</th>
                            <th rowspan="2" class="align-middle text-start">Nama Faskes</th>
                            <th rowspan="2" class="align-middle">SKM</th>
                            <th colspan="{{ count($kodeUnsur) }}" class="unsur-header">Nilai Unsur Pelayanan</th>
                        </tr>
                        <tr>
                            @foreach ($kodeUnsur as $kode)
                                <th class="unsur-header">{{ $kode }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($semuaAnggota as $anggota)
                            <tr data-skm="{{ $anggota['nilai_akhir'] }}" data-nama="{{ mb_strtolower($anggota['nama']) }}" data-rank="{{ $anggota['peringkat'] }}">
                                <td><span class="rank-badge {{ $anggota['peringkat'] <= 3 ? 'rank-' . $anggota['peringkat'] : 'rank-default' }}">{{ $anggota['peringkat'] }}</span></td>
                                <td class="text-start">
                                    <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $anggota['id'], 'periode_survei_id' => $periode->id]) }}" class="text-decoration-none fw-semibold">
                                        {{ $anggota['nama'] }}
                                    </a>
                                </td>
                                <td><strong>{{ $anggota['nilai_akhir'] }}</strong></td>
                                @foreach ($kodeUnsur as $kode)
                                    @php $nilai = $anggota['per_unsur'][$kode] ?? 0; @endphp
                                    <td>
                                        @if ($nilai >= 88.31)
                                            <span class="text-success fw-semibold">{{ $nilai }}</span>
                                        @elseif ($nilai >= 76.61)
                                            <span>{{ $nilai }}</span>
                                        @elseif ($nilai >= 65.00)
                                            <span class="text-warning fw-semibold">{{ $nilai }}</span>
                                        @else
                                            <span class="text-danger fw-semibold">{{ $nilai }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function getRankClass(n) {
                if (n === 1) return 'rank-1';
                if (n === 2) return 'rank-2';
                if (n === 3) return 'rank-3';
                return 'rank-default';
            }

            function sortTable(tableId, containerId) {
                var table = document.getElementById(tableId);
                var tbody = table.querySelector('tbody');
                var rows = Array.from(tbody.querySelectorAll('tr'));
                var btns = document.querySelectorAll('#' + containerId + ' .filter-btn');

                btns.forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        btns.forEach(function (b) { b.classList.remove('active'); });
                        btn.classList.add('active');

                        var mode = btn.getAttribute('data-sort');

                        rows.sort(function (a, b) {
                            if (mode === 'skm-desc') {
                                return (parseFloat(b.getAttribute('data-skm')) || 0) - (parseFloat(a.getAttribute('data-skm')) || 0);
                            } else if (mode === 'skm-asc') {
                                return (parseFloat(a.getAttribute('data-skm')) || 0) - (parseFloat(b.getAttribute('data-skm')) || 0);
                            } else {
                                // 'nama' — urut alfabetis
                                return a.getAttribute('data-nama').localeCompare(b.getAttribute('data-nama'));
                            }
                        });

                        rows.forEach(function (row, index) {
                            tbody.appendChild(row);
                            var badge = row.querySelector('.rank-badge');
                            if (badge) {
                                // Saat diurut alfabetis, kembalikan peringkat asli
                                // dari atribut data-rank (bukan posisi baris).
                                var rank = mode === 'nama'
                                    ? parseInt(row.getAttribute('data-rank'), 10) || (index + 1)
                                    : index + 1;
                                badge.textContent = rank;
                                badge.className = 'rank-badge ' + getRankClass(rank);
                            }
                        });
                    });
                });
            }

            sortTable('tabel-ranking-faskes', 'filterRanking');
            sortTable('tabel-unsur-faskes', 'filterUnsur');
        });
    </script>
@endsection
