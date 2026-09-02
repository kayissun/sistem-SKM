@extends('layouts.dinkes')

@section('title', 'Klaster Performa Unit')

@section('content')
    <style>
        /* ============ COLOR TOKENS ============ */
        :root {
            --purple-900: #180733;
            --purple-800: #2E1065;
            --purple-700: #6D28D9;
            --purple-600: #7C3AED;
            --purple-500: #8B5CF6;
            --purple-100: #EDE9FE;
            --gold-700: #A66A0E;
            --gold-600: #C88719;
            --gold-400: #E4A63B;
            --gold-100: #FCF1DC;
            --surface-0: #FFFFFF;
            --surface-1: #FAF8FF;
            --surface-2: #F3EEFF;
            --ink: #14102B;
            --ink-muted: #625B78;
        }

        /* ============ PAGE HEAD ============ */
        .cluster-head { 
            display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:20px;
        }
        .cluster-head h3 { 
            color:var(--purple-900); font-weight:800; margin:0 0 4px; font-size:1.75rem;
        }
        .cluster-head p { 
            color:var(--ink-muted); margin:0; font-size:.88rem; 
        }
        .cluster-actions { 
            display:flex; gap:8px; flex-wrap:wrap; 
        }
        .cluster-actions .btn { 
            font-size:.8rem; font-weight:600; border-radius:10px; transition:.15s;
        }
        .cluster-actions .btn-outline-danger { 
            border-color:rgba(139,40,217,.2); color:var(--purple-700); 
        }
        .cluster-actions .btn-outline-danger:hover { 
            background:var(--purple-700); border-color:var(--purple-700); color:#fff;
        }
        .cluster-actions .btn-outline-success { 
            border-color:rgba(200,135,25,.2); color:var(--gold-600);
        }
        .cluster-actions .btn-outline-success:hover { 
            background:var(--gold-600); border-color:var(--gold-600); color:#fff;
        }

        /* ============ FILTER FORM ============ */
        .cluster-filter { 
            border-radius:14px; border:none; 
        }
        .cluster-filter .form-label { 
            font-size:.72rem; letter-spacing:.02em; color:var(--ink-muted); font-weight:700;
        }
        .cluster-filter .form-select { 
            border-color:rgba(109,40,217,.15); background:var(--surface-1); border-radius:10px; 
            font-size:.9rem; color:var(--purple-900);
        }
        .cluster-filter .form-select:focus { 
            box-shadow: 0 0 0 .2rem rgba(109,40,217,.12);
        }
        .cluster-filter .btn-primary { 
            background:var(--purple-700); border-color:var(--purple-700); border-radius:10px; font-weight:600;
        }
        .cluster-filter .btn-primary:hover { 
            background:var(--purple-800); border-color:var(--purple-800);
        }

        /* ============ CLUSTER CARDS ============ */
        .cluster-card {
            border:none; border-radius:14px; box-shadow:0 2px 6px rgba(46,16,101,.04);
            transition:transform .15s, box-shadow .15s;
        }
        .cluster-card:hover {
            transform:translateY(-2px); box-shadow:0 6px 16px rgba(46,16,101,.08);
        }
        .cluster-card .card-header {
            background:var(--surface-1); border-bottom:1px solid rgba(109,40,217,.1); border-radius:14px 14px 0 0;
            padding:1rem;
        }
        .cluster-label { 
            font-size:.72rem; font-weight:700; letter-spacing:.03em; padding:4px 10px;
            border-radius:99px; background:var(--purple-700); color:#fff;
        }
        .cluster-member { 
            border-top:1px solid rgba(109,40,217,.08); padding:10px 0; 
        }
        .cluster-member:first-child { border-top:none; }
        .cluster-member a {
            color:var(--purple-900); font-weight:600; text-decoration:none; transition:.15s;
        }
        .cluster-member a:hover {
            color:var(--purple-700);
        }
        .cluster-member .text-muted {
            color:var(--ink-muted) !important; font-size:.75rem;
        }

        /* ============ CLUSTER INSIGHT ============ */
        .cluster-insight {
            background:linear-gradient(135deg, var(--surface-1), var(--purple-100));
            border-left:4px solid var(--purple-700);
            border-radius:10px;
            padding:10px 12px;
            font-size:.82rem;
            color:var(--purple-900);
            display:flex; gap:8px;
        }
        .cluster-insight i {
            color:var(--purple-700); flex-shrink:0;
        }

        /* ============ CLUSTER TREND ============ */
        .cluster-trend { 
            font-size:.72rem; line-height:1.9; text-align:right; 
        }
        .cluster-trend span { 
            display:inline-block; margin-left:4px; padding:3px 9px; border-radius:6px; 
            background:var(--surface-2); color:var(--ink-muted); white-space:nowrap; 
            border:1px solid rgba(109,40,217,.1); font-size:.7rem; font-weight:500;
        }
        .cluster-trend span.aktif { 
            background:var(--purple-700); color:#fff; font-weight:700; border-color:var(--purple-700);
        }

        /* ============ RANKING TABLES ============ */
        .cluster-table { font-size:.82rem; }
        .cluster-table thead {
            background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.1);
        }
        .cluster-table th { 
            color:var(--purple-900); font-weight:700; padding:.75rem .85rem; white-space:nowrap;
        }
        .cluster-table tbody tr {
            transition:background .15s; border-bottom:1px solid rgba(109,40,217,.05);
        }
        .cluster-table tbody tr:hover {
            background:var(--surface-1);
        }
        .cluster-table td { padding:.75rem .85rem; }
        .cluster-table a { 
            color:var(--purple-700); font-weight:600; text-decoration:none; transition:.15s;
        }
        .cluster-table a:hover {
            color:var(--purple-800); text-decoration:underline;
        }

        /* ============ RANK BADGES ============ */
        .rank-badge { 
            display:inline-flex; align-items:center; justify-content:center; 
            width:32px; height:32px; border-radius:8px; font-weight:700; font-size:.8rem;
        }
        .rank-1 { 
            background:linear-gradient(135deg, #FEF3C7, #FCD34D); color:var(--gold-700); border:1px solid #FDE68A;
        }
        .rank-2 { 
            background:#E5E7EB; color:#374151; border:1px solid #D1D5DB;
        }
        .rank-3 { 
            background:linear-gradient(135deg, #FED7AA, #FDBA74); color:var(--gold-700); border:1px solid #FED7AA;
        }
        .rank-default { 
            background:var(--surface-2); color:var(--ink-muted); border:1px solid rgba(109,40,217,.1);
        }

        /* ============ FILTER BUTTONS ============ */
        .filter-btn { 
            padding:6px 14px; border-radius:8px; font-size:.8rem; font-weight:600; 
            border:1.5px solid rgba(109,40,217,.15); background:#fff; color:var(--ink-muted); 
            cursor:pointer; transition:all .15s;
        }
        .filter-btn:hover:not(.active) { 
            border-color:var(--purple-700); color:var(--purple-700); background:var(--surface-1);
        }
        .filter-btn.active { 
            background:var(--purple-700); color:#fff; border-color:var(--purple-700);
        }

        /* ============ UNSUR TABLE ============ */
        .unsur-header { 
            background:linear-gradient(135deg, var(--gold-100), var(--surface-2)); 
            color:var(--gold-700); font-weight:700; border-bottom:2px solid var(--gold-400);
        }
        .unsur-cell-excellent {
            background:linear-gradient(135deg, #ECFDF5, #D1FAE5);
            color:#065F46; font-weight:600;
        }
        .unsur-cell-good {
            color:#1E40AF; font-weight:500;
        }
        .unsur-cell-fair {
            background:linear-gradient(135deg, #FFFBEB, #FEF3C7);
            color:var(--gold-700); font-weight:600;
        }
        .unsur-cell-poor {
            background:linear-gradient(135deg, #FEF2F2, #FECACA);
            color:#991B1B; font-weight:600;
        }

        /* ============ STATS BAR ============ */
        .cluster-stats {
            display:flex; gap:20px; flex-wrap:wrap; font-size:.9rem; color:var(--ink-muted);
        }
        .cluster-stats span strong {
            color:var(--purple-900); font-weight:700;
        }

        /* ============ ALERT IMPROVEMENTS ============ */
        .alert-warning {
            background:linear-gradient(135deg, #FFFBEB, #FEF3C7);
            border:1px solid var(--gold-400);
            color:var(--gold-700);
            border-radius:10px; font-weight:500;
        }
        .alert-secondary {
            background:var(--surface-1); border:1px solid rgba(109,40,217,.15);
            border-radius:10px; color:var(--ink-muted);
        }
    </style>

    <div class="cluster-head">
        <div>
            <span style="display:inline-flex; align-items:center; gap:6px; font-size:.68rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--purple-700); background:var(--purple-100); padding:3px 10px; border-radius:99px; margin-bottom:8px;">
                <i class="fa-solid fa-layer-group"></i> Klaster Performa
            </span>
            <h3>Klaster Performa Faskes</h3>
            <p>Pantau Klaster performa faskes per periode survei.</p>
        </div>
        @if ($periode)
            <div class="cluster-actions">
                <a href="{{ route('dinkes.klaster.export-pdf', request()->query()) }}" class="btn btn-outline-danger">
                    <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('dinkes.klaster.export-excel', request()->query()) }}" class="btn btn-outline-success">
                    <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                </a>
            </div>
        @endif
    </div>

    <form method="GET" class="card cluster-filter border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label"><i class="fa-solid fa-calendar-days me-1"></i> Periode Survei</label>
                <select name="periode_survei_id" class="form-select">
                @foreach ($daftarPeriode as $p)
                    <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                        {{ $p->nama }} @if($p->is_active) (aktif) @endif
                    </option>
                @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fa-solid fa-object-group me-1"></i> Jumlah kelompok</label>
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

        <div class="cluster-stats mb-4">
            <span><i class="fa-solid fa-hospital me-1" style="color:var(--purple-700)"></i><strong>{{ $jumlahSampel }}</strong> unit dianalisis</span>
            <span><i class="fa-solid fa-object-group me-1" style="color:var(--purple-700)"></i><strong>{{ $jumlahKlaster }}</strong> kelompok terbentuk</span>
            <span><i class="fa-solid fa-calendar-days me-1" style="color:var(--purple-700)"></i><strong>{{ $periode->nama }}</strong></span>
        </div>

        {{-- ============================================================ --}}
        {{-- KARTU KELOMPOK KLASTER (radar + insight + anggota + tren)  --}}
        {{-- ============================================================ --}}
        <div class="row g-3 mb-4">
            @foreach ($kelompok as $kel)
                @php $insightKelompok = $insight->firstWhere('cluster', $kel['label']); @endphp
                <div class="col-lg-6">
                    <div class="card cluster-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="cluster-label">{{ $kel['label'] }}</span>
                                <span class="ms-2 small" style="color:var(--ink-muted)">{{ $kel['anggota']->count() }} unit</span>
                            </div>
                            <div class="small" style="color:var(--ink-muted)">
                                Rata-rata SKM <span style="color:var(--purple-900); font-weight:700; font-size:1.1em">{{ $kel['rata_rata_skor'] }}</span>
                            </div>
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
        <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.1); border-radius:14px 14px 0 0">
                <span style="font-weight:700; color:var(--purple-900); font-size:1rem"><i class="fa-solid fa-ranking-star me-2" style="color:var(--purple-700)"></i>Ranking Faskes</span>
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
        <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.1); border-radius:14px 14px 0 0">
                <div>
                    <div style="font-weight:700; color:var(--purple-900); font-size:1rem"><i class="fa-solid fa-layer-group me-2" style="color:var(--purple-700)"></i>Nilai Unsur Pelayanan Per Faskes</div>
                    <div class="small mt-1" style="color:var(--ink-muted)">Nilai NRR (skala 100) per unsur pelayanan untuk seluruh faskes.</div>
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
                                            <span class="px-2 py-1 rounded unsur-cell-excellent">{{ $nilai }}</span>
                                        @elseif ($nilai >= 76.61)
                                            <span class="unsur-cell-good">{{ $nilai }}</span>
                                        @elseif ($nilai >= 65.00)
                                            <span class="px-2 py-1 rounded unsur-cell-fair">{{ $nilai }}</span>
                                        @else
                                            <span class="px-2 py-1 rounded unsur-cell-poor">{{ $nilai }}</span>
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
