@extends('layouts.dinkes')

@section('title', 'Klaster Performa Unit')

@section('content')
    {{-- Memuat Chart.js dari CDN untuk diagram komparasi seluruh faskes --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        /* ============ COLOR TOKENS ============ */
        :root {
            --purple-950: #0F0426;
            --purple-900: #180733;
            --purple-800: #2E1065;
            --purple-700: #6D28D9;
            --purple-600: #7C3AED;
            --purple-500: #8B5CF6;
            --purple-100: #EDE9FE;
            --purple-50:  #FAF8FF;
            --gold-700:   #A66A0E;
            --gold-600:   #C88719;
            --gold-400:   #E4A63B;
            --gold-100:   #FCF1DC;
            --surface-0:  #FFFFFF;
            --surface-1:  #FAF8FF;
            --surface-2:  #F3EEFF;
            --ink:        #14102B;
            --ink-muted:  #625B78;
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

        /* ============ STATS BAR ============ */
        .cluster-stats {
            display:flex; gap:16px; flex-wrap:wrap; font-size:.88rem; color:var(--ink-muted);
        }
        .cluster-stats-item {
            background:#fff; padding:8px 16px; border-radius:12px; border:1px solid rgba(109,40,217,.08);
            display:inline-flex; align-items:center; gap:8px; box-shadow:0 1px 3px rgba(46,16,101,.03);
        }
        .cluster-stats-item strong {
            color:var(--purple-900); font-weight:700;
        }

        /* ============ CLUSTER SUMMARY CARDS ============ */
        .cluster-summary-card {
            border:none; border-radius:14px; box-shadow:0 2px 8px rgba(46,16,101,.04);
            background:#fff; transition:transform .15s, box-shadow .15s; height:100%;
            display:flex; flex-direction:column; border-top:4px solid var(--purple-700);
        }
        .cluster-summary-card:hover {
            transform:translateY(-2px); box-shadow:0 6px 18px rgba(46,16,101,.08);
        }
        .cluster-summary-card .card-head {
            padding:14px 18px 10px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;
        }
        .cluster-summary-card .card-body {
            padding:10px 18px 16px; flex-grow:1; display:flex; flex-direction:column;
        }
        .cluster-badge {
            font-size:.72rem; font-weight:700; letter-spacing:.03em; padding:4px 10px;
            border-radius:99px; display:inline-flex; align-items:center; gap:5px;
        }
        .cluster-chip {
            display:inline-flex; align-items:center; gap:6px; font-size:.75rem; padding:4px 9px;
            border-radius:8px; background:var(--surface-1); border:1px solid rgba(109,40,217,.1);
            color:var(--purple-900); text-decoration:none; margin-bottom:4px; margin-right:4px;
            transition:.15s;
        }
        .cluster-chip:hover {
            background:var(--purple-100); color:var(--purple-700); border-color:var(--purple-700);
        }
        .cluster-chip-skm {
            font-weight:700; color:var(--purple-700); background:#fff; padding:1px 5px; border-radius:4px; font-size:.7rem;
        }

        /* ============ CHART CONTAINER ============ */
        .chart-card {
            border:none; border-radius:14px; box-shadow:0 2px 10px rgba(46,16,101,.04);
            background:#fff; overflow:hidden; margin-bottom:24px;
        }
        .chart-card-header {
            padding:16px 20px; background:var(--surface-1); border-bottom:1px solid rgba(109,40,217,.1);
            display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;
        }
        .chart-card-title {
            font-weight:700; color:var(--purple-900); font-size:1.05rem; margin:0;
            display:flex; align-items:center; gap:8px;
        }

        /* ============ HEATMAP MATRIX ============ */
        .heatmap-container {
            overflow-x:auto; -webkit-overflow-scrolling:touch;
        }
        .heatmap-table {
            width:100%; border-collapse:collapse; font-size:.78rem; text-align:center;
        }
        .heatmap-table th, .heatmap-table td {
            padding:8px 6px; border:1px solid rgba(109,40,217,.08); white-space:nowrap;
        }
        .heatmap-table th {
            background:var(--surface-1); color:var(--purple-900); font-weight:700; font-size:.75rem;
        }
        .heatmap-cell {
            font-weight:600; font-size:.78rem; border-radius:4px; transition:transform .12s, box-shadow .12s;
            cursor:default; display:inline-block; width:100%; padding:4px 0;
        }
        .heatmap-cell:hover {
            transform:scale(1.08); z-index:2; box-shadow:0 2px 6px rgba(0,0,0,.15);
        }

        /* Mutu Skala 1-4 Coloring Sesuai Spesifikasi User:
           A (Sangat Baik)  88,31 - 100,00 = Hijau
           B (Baik)         76,61 - 88,30  = Biru
           C (Kurang Baik)  65,00 - 76,60  = Kuning
           D (Tidak Baik)   25,00 - 64,99  = Merah
        */
        .cell-mutu-a { background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; } /* A - Sangat Baik (Hijau) */
        .cell-mutu-b { background:#EFF6FF; color:#1E40AF; border:1px solid #BFDBFE; } /* B - Baik (Biru) */
        .cell-mutu-c { background:#FEF9C3; color:#854D0E; border:1px solid #FDE047; } /* C - Kurang Baik (Kuning) */
        .cell-mutu-d { background:#FEF2F2; color:#991B1B; border:1px solid #FECACA; } /* D - Tidak Baik (Merah) */

        /* ============ UNIFIED TABLE ============ */
        .unified-table-card {
            border:none; border-radius:14px; box-shadow:0 2px 10px rgba(46,16,101,.04);
            background:#fff; overflow:hidden; margin-bottom:24px;
        }
        .unified-table {
            font-size:.82rem; margin-bottom:0;
        }
        .unified-table thead {
            background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.12);
        }
        .unified-table th {
            color:var(--purple-900); font-weight:700; padding:.75rem .65rem; white-space:nowrap;
        }
        .unified-table td {
            padding:.65rem .65rem; vertical-align:middle; border-bottom:1px solid rgba(109,40,217,.06);
        }
        .unified-table tbody tr {
            transition:background .12s;
        }
        .unified-table tbody tr:hover {
            background:var(--surface-1);
        }

        /* Sticky Columns */
        .sticky-col-rank {
            position:sticky; left:0; background:#fff; z-index:3;
        }
        .sticky-col-name {
            position:sticky; left:50px; background:#fff; z-index:3;
        }
        .unified-table thead .sticky-col-rank,
        .unified-table thead .sticky-col-name {
            background:var(--surface-1); z-index:4;
        }
        .unified-table tbody tr:hover .sticky-col-rank,
        .unified-table tbody tr:hover .sticky-col-name {
            background:var(--surface-1);
        }

        /* Rank Badges */
        .rank-badge {
            display:inline-flex; align-items:center; justify-content:center;
            width:30px; height:30px; border-radius:8px; font-weight:800; font-size:.8rem;
        }
        .rank-1 {
            background:linear-gradient(135deg, #FEF3C7, #FCD34D); color:var(--gold-700); border:1px solid #FDE68A; box-shadow:0 2px 4px rgba(200,135,25,.15);
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

        /* Filter Controls */
        .filter-btn {
            padding:5px 12px; border-radius:8px; font-size:.78rem; font-weight:600;
            border:1.5px solid rgba(109,40,217,.15); background:#fff; color:var(--ink-muted);
            cursor:pointer; transition:all .15s; white-space:nowrap;
        }
        .filter-btn:hover:not(.active) {
            border-color:var(--purple-700); color:var(--purple-700); background:var(--surface-1);
        }
        .filter-btn.active {
            background:var(--purple-700); color:#fff; border-color:var(--purple-700);
        }

        .toggle-unsur-btn {
            font-size:.78rem; font-weight:600; padding:5px 12px; border-radius:8px;
            border:1.5px solid rgba(109,40,217,.2); background:var(--purple-50); color:var(--purple-700);
            cursor:pointer; transition:all .15s;
        }
        .toggle-unsur-btn:hover {
            background:var(--purple-100); border-color:var(--purple-700);
        }

        .col-unsur-header {
            background:linear-gradient(135deg, var(--gold-100), var(--surface-2));
            color:var(--gold-700); font-weight:700;
        }
    </style>

    @php
        // Palette warna tematik untuk klaster (hingga 6 klaster)
        $paletteKlaster = [
            0 => ['bg' => '#EDE9FE', 'border' => '#7C3AED', 'text' => '#5B21B6'],
            1 => ['bg' => '#DBEAFE', 'border' => '#2563EB', 'text' => '#1D4ED8'],
            2 => ['bg' => '#CCFBF1', 'border' => '#0D9488', 'text' => '#0F766E'],
            3 => ['bg' => '#FEF3C7', 'border' => '#D97706', 'text' => '#B45309'],
            4 => ['bg' => '#FFEDD5', 'border' => '#EA580C', 'text' => '#C2410C'],
            5 => ['bg' => '#FFE4E6', 'border' => '#E11D48', 'text' => '#BE123C'],
        ];

        // Petakan label klaster ke index warna
        $petaWarnaKlaster = [];
        foreach ($kelompok as $idx => $k) {
            $petaWarnaKlaster[$k['label']] = $paletteKlaster[$idx % count($paletteKlaster)];
        }

        // Rata-rata SKM keseluruhan faskes
        $rataRataSemua = $semuaAnggota->isNotEmpty() ? round($semuaAnggota->avg('nilai_akhir'), 2) : 0;

        // Peta warna badge Mutu berdasarkan HURUF mutu asli dari backend (A/B/C/D),
        // BUKAN dihitung ulang dari nilai SKM di view — supaya warna badge selalu
        // sinkron 100% dengan teks mutu yang ditampilkan, walau ada perbedaan
        // pembulatan/ambang batas di SkmCalculatorService.
        $kelasBadgeMutu = [
            'A' => 'bg-success-subtle text-success border border-success-subtle', // Sangat Baik - Hijau
            'B' => 'bg-primary-subtle text-primary border border-primary-subtle', // Baik - Biru
            'C' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', // Kurang Baik - Kuning
            'D' => 'bg-danger-subtle text-danger border border-danger-subtle', // Tidak Baik - Merah
        ];
    @endphp

    <div class="cluster-head">
        <div>
            <span style="display:inline-flex; align-items:center; gap:6px; font-size:.68rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--purple-700); background:var(--purple-100); padding:3px 10px; border-radius:99px; margin-bottom:8px;">
                <i class="fa-solid fa-layer-group"></i> Klaster Performa
            </span>
            <h3>Klaster Performa Faskes</h3>
            <p>Analisis pengelompokan performa unit fasilitas kesehatan dan pemetaan nilai 9 unsur pelayanan.</p>
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

    {{-- Filter Periode & Jumlah Klaster --}}
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
                <label class="form-label"><i class="fa-solid fa-object-group me-1"></i> Jumlah Kelompok</label>
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
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $peringatanKualitas }}
            </div>
        @endif

        {{-- Statistik Ringkas --}}
        <div class="cluster-stats mb-4">
            <div class="cluster-stats-item">
                <i class="fa-solid fa-hospital" style="color:var(--purple-700)"></i>
                <span><strong>{{ $jumlahSampel }}</strong> Unit Dianalisis</span>
            </div>
            <div class="cluster-stats-item">
                <i class="fa-solid fa-object-group" style="color:var(--purple-700)"></i>
                <span><strong>{{ $jumlahKlaster }}</strong> Kelompok Klaster</span>
            </div>
            <div class="cluster-stats-item">
                <i class="fa-solid fa-chart-line" style="color:var(--purple-700)"></i>
                <span>Rata-rata SKM: <strong>{{ $rataRataSemua }}</strong></span>
            </div>
            <div class="cluster-stats-item">
                <i class="fa-solid fa-calendar-check" style="color:var(--purple-700)"></i>
                <span>Periode: <strong>{{ $periode->nama }}</strong></span>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- KARTU RINGKASAN KLASTER (CLEAN, INFORMATIF, MODERN)         --}}
        {{-- ============================================================ --}}
        <div class="row g-3 mb-4">
            @foreach ($kelompok as $idx => $kel)
                @php
                    $insightKelompok = $insight->firstWhere('cluster', $kel['label']);
                    $warna = $petaWarnaKlaster[$kel['label']] ?? $paletteKlaster[0];
                @endphp
                <div class="col-md-6 col-xl-{{ $jumlahKlaster <= 3 ? (12 / $jumlahKlaster) : '3' }}">
                    <div class="cluster-summary-card" style="border-top-color: {{ $warna['border'] }};">
                        <div class="card-head" style="background: {{ $warna['bg'] }};">
                            <div>
                                <span class="cluster-badge" style="background: {{ $warna['border'] }}; color: #fff;">
                                    <i class="fa-solid fa-layer-group"></i> {{ $kel['label'] }}
                                </span>
                                <div class="small mt-1 text-muted">{{ $kel['anggota']->count() }} Faskes</div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted" style="font-size:.7rem">Rata-rata SKM</div>
                                <div style="font-size:1.25rem; font-weight:800; color: {{ $warna['text'] }};">
                                    {{ $kel['rata_rata_skor'] }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($insightKelompok)
                                <div class="p-2 mb-3 rounded" style="background: {{ $warna['bg'] }}; border-left: 3px solid {{ $warna['border'] }}; font-size:.78rem; color: {{ $warna['text'] }};">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                    <strong>Fokus Perbaikan:</strong> {{ $insightKelompok['isu_utama'] }} ({{ $insightKelompok['isu_utama_nama'] }})
                                </div>
                            @endif

                            <div class="small fw-bold text-muted mb-2" style="font-size:.72rem">ANGGOTA KLASTER:</div>
                            <div class="d-flex flex-wrap align-items-center">
                                @forelse ($kel['anggota'] as $anggota)
                                    <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $anggota['id'], 'periode_survei_id' => $periode->id]) }}"
                                       class="cluster-chip"
                                       title="Lihat detail laporan {{ $anggota['nama'] }}">
                                        <span>{{ Str::limit($anggota['nama'], 18) }}</span>
                                        <span class="cluster-chip-skm">{{ $anggota['nilai_akhir'] }}</span>
                                    </a>
                                @empty
                                    <span class="text-muted small">Tidak ada anggota</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ============================================================ --}}
        {{-- VISUALISASI 1: DIAGRAM KOMPARASI SKM SELURUH FASKES (BAR)    --}}
        {{-- ============================================================ --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h5 class="chart-card-title">
                        <i class="fa-solid fa-chart-column" style="color:var(--purple-700)"></i>
                        Diagram Komparasi Nilai SKM Seluruh Faskes
                    </h5>
                    <div class="text-muted small mt-1">
                        Membandingkan nilai SKM setiap faskes dengan warna batang yang diklasifikasikan berdasarkan kategori mutu: Hijau (A - Sangat Baik), Biru (B - Baik), Kuning (C - Kurang Baik), dan Merah (D - Tidak Baik).
                    </div>
                </div>
                <div class="d-flex gap-1 flex-wrap" id="chartKlasterFilter">
                    <button type="button" class="filter-btn active" data-filter="all">Semua Faskes</button>
                    @foreach ($kelompok as $kel)
                        <button type="button" class="filter-btn" data-filter="{{ $kel['label'] }}">
                            {{ $kel['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="p-3">
                <div style="position:relative; height:{{ max(350, count($semuaAnggota) * 28) }}px; width:100%;">
                    <canvas id="canvasChartSkm"></canvas>
                </div>
                {{-- Legend Mutu Sesuai Permintaan User --}}
                <div class="d-flex justify-content-center gap-4 mt-3 flex-wrap small text-muted">
                    <span>
                        <span style="display:inline-block; width:12px; height:12px; background:#10B981; border-radius:3px; margin-right:5px; vertical-align:middle;"></span>
                        <strong>A (Sangat Baik)</strong> 88,31 - 100,00
                    </span>
                    <span>
                        <span style="display:inline-block; width:12px; height:12px; background:#3B82F6; border-radius:3px; margin-right:5px; vertical-align:middle;"></span>
                        <strong>B (Baik)</strong> 76,61 - 88,30
                    </span>
                    <span>
                        <span style="display:inline-block; width:12px; height:12px; background:#EAB308; border-radius:3px; margin-right:5px; vertical-align:middle;"></span>
                        <strong>C (Kurang Baik)</strong> 65,00 - 76,60
                    </span>
                    <span>
                        <span style="display:inline-block; width:12px; height:12px; background:#EF4444; border-radius:3px; margin-right:5px; vertical-align:middle;"></span>
                        <strong>D (Tidak Baik)</strong> 25,00 - 64,99
                    </span>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- VISUALISASI 2: PETA PANAS (HEATMAP) 9 UNSUR PER FASKES       --}}
        {{-- ============================================================ --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h5 class="chart-card-title">
                        <i class="fa-solid fa-braille" style="color:var(--purple-700)"></i>
                        Peta Kinerja 9 Unsur Pelayanan Seluruh Faskes (Skala IKM 1 - 4)
                    </h5>
                    <div class="text-muted small mt-1">
                        Matriks nilai IKM per unsur (1.000 - 4.000) untuk memetakan langsung titik kelemahan dan keunggulan pelayanan di setiap unit.
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap small">
                    <span class="badge cell-mutu-a px-2 py-1">≥ 3.532 (A - Hijau)</span>
                    <span class="badge cell-mutu-b px-2 py-1">3.064 - 3.531 (B - Biru)</span>
                    <span class="badge cell-mutu-c px-2 py-1">2.600 - 3.063 (C - Kuning)</span>
                    <span class="badge cell-mutu-d px-2 py-1">&lt; 2.600 (D - Merah)</span>
                </div>
            </div>
            <div class="p-3">
                <div class="heatmap-container">
                    <table class="heatmap-table">
                        <thead>
                            <tr>
                                <th style="width:40px">Rank</th>
                                <th class="text-start" style="min-width:180px">Fasilitas Kesehatan</th>
                                <th style="width:80px">Klaster</th>
                                <th style="width:70px">SKM</th>
                                @foreach ($kodeUnsur as $kode)
                                    <th style="min-width:65px" title="{{ $namaUnsur[$kode] ?? $kode }}">
                                        {{ $kode }}
                                        <div style="font-size:.65rem; font-weight:normal; opacity:.8">{{ Str::limit($namaUnsur[$kode] ?? $kode, 8) }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($semuaAnggota as $anggota)
                                @php
                                    $warna = $petaWarnaKlaster[$anggota['klaster_label'] ?? ''] ?? $paletteKlaster[0];
                                @endphp
                                <tr>
                                    <td>
                                        <span class="rank-badge {{ $anggota['peringkat'] <= 3 ? 'rank-' . $anggota['peringkat'] : 'rank-default' }}" style="width:24px; height:24px; font-size:.75rem">
                                            {{ $anggota['peringkat'] }}
                                        </span>
                                    </td>
                                    <td class="text-start fw-semibold">
                                        <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $anggota['id'], 'periode_survei_id' => $periode->id]) }}" style="color:var(--purple-900)">
                                            {{ $anggota['nama'] }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:{{ $warna['bg'] }}; color:{{ $warna['text'] }}; border:1px solid {{ $warna['border'] }}; font-size:.7rem">
                                            {{ $anggota['klaster_label'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold" style="color:var(--purple-900)">{{ $anggota['nilai_akhir'] }}</td>
                                    @foreach ($kodeUnsur as $kode)
                                        @php
                                            $nrr = (float) ($anggota['per_unsur'][$kode] ?? 0);
                                            // Evaluasi Mutu Skala 1 - 4:
                                            // A: Hijau (>= 3.532)
                                            // B: Biru (3.064 - 3.531)
                                            // C: Kuning (2.600 - 3.063)
                                            // D: Merah (< 2.600)
                                            if ($nrr >= 3.532) {
                                                $cellClass = 'cell-mutu-a';
                                                $mutuTeks = 'A (Sangat Baik)';
                                            } elseif ($nrr >= 3.064) {
                                                $cellClass = 'cell-mutu-b';
                                                $mutuTeks = 'B (Baik)';
                                            } elseif ($nrr >= 2.600) {
                                                $cellClass = 'cell-mutu-c';
                                                $mutuTeks = 'C (Kurang Baik)';
                                            } else {
                                                $cellClass = 'cell-mutu-d';
                                                $mutuTeks = 'D (Tidak Baik)';
                                            }
                                        @endphp
                                        <td>
                                            <span class="heatmap-cell {{ $cellClass }}"
                                                  title="{{ $anggota['nama'] }} | {{ $kode }} - {{ $namaUnsur[$kode] ?? '' }}: {{ number_format($nrr, 3) }} ({{ $mutuTeks }})">
                                                {{ number_format($nrr, 3) }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- Baris Rata-rata Unsur Se-Kabupaten/Kota --}}
                        <tfoot>
                            <tr style="background:var(--surface-2); font-weight:bold;">
                                <td colspan="4" class="text-end pe-3 text-uppercase" style="color:var(--purple-900)">
                                    <i class="fa-solid fa-calculator me-1"></i> Rata-rata Seluruh Unit:
                                </td>
                                @foreach ($kodeUnsur as $kode)
                                    @php
                                        $avgNrr = $semuaAnggota->isNotEmpty()
                                            ? $semuaAnggota->avg(fn ($a) => (float) ($a['per_unsur'][$kode] ?? 0))
                                            : 0;
                                        if ($avgNrr >= 3.532) $cClass = 'cell-mutu-a'; // Hijau
                                        elseif ($avgNrr >= 3.064) $cClass = 'cell-mutu-b'; // Biru
                                        elseif ($avgNrr >= 2.600) $cClass = 'cell-mutu-c'; // Kuning
                                        else $cClass = 'cell-mutu-d'; // Merah
                                    @endphp
                                    <td>
                                        <span class="heatmap-cell {{ $cClass }}" title="Rata-rata daerah {{ $kode }}: {{ number_format($avgNrr, 3) }}">
                                            {{ number_format($avgNrr, 3) }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TABEL TERPADU (MERGE 2 TABEL MENJADI 1 TABEL RESPONSIF)     --}}
        {{-- ============================================================ --}}
        <div class="unified-table-card">
            <div class="chart-card-header">
                <div>
                    <h5 class="chart-card-title">
                        <i class="fa-solid fa-table-list" style="color:var(--purple-700)"></i>
                        Tabel Terpadu Kinerja & Unsur Pelayanan Faskes
                    </h5>
                    <div class="text-muted small mt-1">
                        Peringkat performa, klasifikasi klaster, nilai SKM, dan rincian IKM per unsur pelayanan (skala 1-4).
                    </div>
                </div>
                {{-- Toolbar Kontrol Tabel --}}
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    {{-- Pencarian Cepat --}}
                    <div class="input-group input-group-sm" style="width:180px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="cariFaskesTabel" class="form-control border-start-0 ps-0" placeholder="Cari unit faskes...">
                    </div>

                    {{-- Filter Klaster --}}
                    <select id="selectFilterKlaster" class="form-select form-select-sm" style="width:140px;">
                        <option value="all">Semua Klaster</option>
                        @foreach ($kelompok as $kel)
                            <option value="{{ $kel['label'] }}">{{ $kel['label'] }}</option>
                        @endforeach
                    </select>

                    {{-- Sort Buttons --}}
                    <div class="d-flex gap-1" id="filterUnifiedTable">
                        <button type="button" class="filter-btn active" data-sort="skm-desc" title="Urutkan dari nilai SKM tertinggi">
                            <i class="fa-solid fa-ranking-star me-1"></i> Peringkat
                        </button>
                        <button type="button" class="filter-btn" data-sort="nama" title="Urutkan alfabetis nama faskes">
                            <i class="fa-solid fa-arrow-down-a-z me-1"></i> Nama
                        </button>
                        <button type="button" class="filter-btn" data-sort="skm-asc" title="Urutkan dari nilai SKM terendah">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Terendah
                        </button>
                    </div>

                    {{-- Toggle Kolom Unsur --}}
                    <button type="button" id="btnToggleUnsur" class="toggle-unsur-btn" title="Sembunyikan atau tampilkan kolom rincian unsur U1 - U9">
                        <i class="fa-solid fa-eye-slash me-1"></i> Sembunyikan Unsur
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabel-terpadu-klaster" class="table unified-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th rowspan="2" class="sticky-col-rank text-center" style="width:50px">Rank</th>
                            <th rowspan="2" class="sticky-col-name text-start" style="min-width:180px">Nama Faskes</th>
                            <th rowspan="2" class="text-center" style="width:100px">Klaster</th>
                            <th rowspan="2" class="text-center" style="width:80px">Nilai SKM</th>
                            <th rowspan="2" class="text-center" style="width:80px">Mutu</th>
                            <th colspan="{{ count($kodeUnsur) }}" class="col-unsur col-unsur-header text-center">
                                Nilai IKM Per Unsur (Skala 1 - 4)
                            </th>
                        </tr>
                        <tr class="col-unsur">
                            @foreach ($kodeUnsur as $kode)
                                <th class="text-center" style="min-width:65px" title="{{ $namaUnsur[$kode] ?? $kode }}">
                                    {{ $kode }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($semuaAnggota as $anggota)
                            @php
                                $warna = $petaWarnaKlaster[$anggota['klaster_label'] ?? ''] ?? $paletteKlaster[0];

                                // Ambil huruf pertama dari nilai mutu asli (mis. "A", "A - Sangat Baik", dst)
                                // supaya cocok dengan key di $kelasBadgeMutu apa pun format string-nya.
                                $mutuHuruf = strtoupper(substr(trim((string) $anggota['mutu']), 0, 1));
                                $badgeMutu = $kelasBadgeMutu[$mutuHuruf] ?? 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                            @endphp
                            <tr data-skm="{{ $anggota['nilai_akhir'] }}"
                                data-nama="{{ mb_strtolower($anggota['nama']) }}"
                                data-rank="{{ $anggota['peringkat'] }}"
                                data-klaster="{{ $anggota['klaster_label'] ?? '' }}">
                                <td class="sticky-col-rank text-center">
                                    <span class="rank-badge {{ $anggota['peringkat'] <= 3 ? 'rank-' . $anggota['peringkat'] : 'rank-default' }}">
                                        {{ $anggota['peringkat'] }}
                                    </span>
                                </td>
                                <td class="sticky-col-name text-start">
                                    <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $anggota['id'], 'periode_survei_id' => $periode->id]) }}"
                                       class="fw-semibold" style="color:var(--purple-700)">
                                        {{ $anggota['nama'] }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge" style="background:{{ $warna['bg'] }}; color:{{ $warna['text'] }}; border:1px solid {{ $warna['border'] }}; font-size:.72rem">
                                        {{ $anggota['klaster_label'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong style="color:var(--purple-900); font-size:.92rem">{{ $anggota['nilai_akhir'] }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeMutu }} px-2 py-1" style="font-size:.72rem">
                                        {{ $anggota['mutu'] }}
                                    </span>
                                </td>
                                @foreach ($kodeUnsur as $kode)
                                    @php
                                        $nrr = (float) ($anggota['per_unsur'][$kode] ?? 0);
                                        // Mutu Unsur:
                                        // A: Hijau (>= 3.532)
                                        // B: Biru (3.064 - 3.531)
                                        // C: Kuning (2.600 - 3.063)
                                        // D: Merah (< 2.600)
                                        if ($nrr >= 3.532) $uClass = 'cell-mutu-a'; // Hijau
                                        elseif ($nrr >= 3.064) $uClass = 'cell-mutu-b'; // Biru
                                        elseif ($nrr >= 2.600) $uClass = 'cell-mutu-c'; // Kuning
                                        else $uClass = 'cell-mutu-d'; // Merah
                                    @endphp
                                    <td class="col-unsur text-center">
                                        <span class="heatmap-cell {{ $uClass }}" title="{{ $kode }} - {{ $namaUnsur[$kode] ?? '' }}: {{ number_format($nrr, 3) }}">
                                            {{ number_format($nrr, 3) }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($dikecualikan->isNotEmpty())
            <div class="alert alert-secondary border-0 shadow-sm" style="background:#fff; border-radius:12px; border-left:4px solid var(--purple-600) !important;">
                <div class="fw-bold" style="color:var(--purple-900)"><i class="fa-solid fa-circle-info me-1 text-primary"></i> Unit Belum Masuk Analisis:</div>
                <ul class="mb-0 mt-1 small" style="color:var(--ink-muted)">
                    @foreach ($dikecualikan as $item)
                        <li><strong>{{ $item['nama'] }}</strong> — {{ $item['alasan'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    {{-- ============================================================ --}}
    {{-- JAVASCRIPT: CHART.JS & TABEL TERPADU KONTROL                --}}
    {{-- ============================================================ --}}
    @if ($periode && $kelompok->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ==========================================
            // DATA PREPARATION DARI SERVER KE JS
            // ==========================================
            const rawData = @json($semuaAnggota);

            // Fungsi Penentuan Warna Berdasarkan Kategori Mutu SKM:
            // A (Sangat Baik)  88,31 - 100,00 = Hijau
            // B (Baik)         76,61 - 88,30  = Biru
            // C (Kurang Baik)  65,00 - 76,60  = Kuning
            // D (Tidak Baik)   25,00 - 64,99  = Merah
            function getMutuColor(skm) {
                const val = parseFloat(skm) || 0;
                if (val >= 88.31) {
                    return { bar: 'rgba(16, 185, 129, 0.85)', border: '#059669', name: 'A (Sangat Baik)' }; // Hijau
                } else if (val >= 76.61) {
                    return { bar: 'rgba(59, 130, 246, 0.85)', border: '#2563EB', name: 'B (Baik)' }; // Biru
                } else if (val >= 65.00) {
                    return { bar: 'rgba(234, 179, 8, 0.85)', border: '#CA8A04', name: 'C (Kurang Baik)' }; // Kuning
                } else {
                    return { bar: 'rgba(239, 68, 68, 0.85)', border: '#DC2626', name: 'D (Tidak Baik)' }; // Merah
                }
            }

            // ==========================================
            // 1. INISIALISASI CHART.JS (BAR KOMPARASI MUTU)
            // ==========================================
            const canvas = document.getElementById('canvasChartSkm');
            if (canvas) {
                const labels = rawData.map(item => item.nama);
                const scores = rawData.map(item => parseFloat(item.nilai_akhir) || 0);
                const bgColors = rawData.map(item => getMutuColor(item.nilai_akhir).bar);
                const borderColors = rawData.map(item => getMutuColor(item.nilai_akhir).border);

                const ctx = canvas.getContext('2d');
                const chartSkm = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nilai SKM',
                            data: scores,
                            backgroundColor: bgColors,
                            borderColor: borderColors,
                            borderWidth: 1,
                            borderRadius: 6,
                            barPercentage: 0.75,
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Horizontal Bar Chart
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#180733',
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    afterTitle: function(context) {
                                        const index = context[0].dataIndex;
                                        const item = rawData[index];
                                        return 'Peringkat: #' + item.peringkat + ' | Klaster: ' + (item.klaster_label || '-');
                                    },
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        const item = rawData[index];
                                        const infoMutu = getMutuColor(item.nilai_akhir).name;
                                        return 'Nilai SKM: ' + context.parsed.x + ' (' + infoMutu + ')';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                min: 0,
                                max: 100,
                                grid: {
                                    color: 'rgba(109, 40, 217, 0.08)'
                                },
                                ticks: {
                                    stepSize: 10,
                                    font: { size: 11 }
                                }
                            },
                            y: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 11, weight: '500' },
                                    color: '#180733'
                                }
                            }
                        }
                    }
                });

                // Filter Chart berdasarkan Klaster (Tetap mempertahankan pewarnaan Mutu)
                const chartFilterBtns = document.querySelectorAll('#chartKlasterFilter .filter-btn');
                chartFilterBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        chartFilterBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        const filter = this.getAttribute('data-filter');
                        const filteredData = filter === 'all'
                            ? rawData
                            : rawData.filter(d => d.klaster_label === filter);

                        chartSkm.data.labels = filteredData.map(d => d.nama);
                        chartSkm.data.datasets[0].data = filteredData.map(d => parseFloat(d.nilai_akhir) || 0);
                        chartSkm.data.datasets[0].backgroundColor = filteredData.map(d => getMutuColor(d.nilai_akhir).bar);
                        chartSkm.data.datasets[0].borderColor = filteredData.map(d => getMutuColor(d.nilai_akhir).border);

                        // Sesuaikan tinggi container secara dinamis
                        canvas.parentElement.style.height = Math.max(300, filteredData.length * 28) + 'px';
                        chartSkm.update();
                    });
                });
            }

            // ==========================================
            // 2. KONTROL TABEL TERPADU (SORT, FILTER, TOGGLE)
            // ==========================================
            const table = document.getElementById('tabel-terpadu-klaster');
            if (!table) return;

            const tbody = table.querySelector('tbody');
            const allRows = Array.from(tbody.querySelectorAll('tr'));
            const sortBtns = document.querySelectorAll('#filterUnifiedTable .filter-btn');
            const selectKlaster = document.getElementById('selectFilterKlaster');
            const inputCari = document.getElementById('cariFaskesTabel');
            const btnToggleUnsur = document.getElementById('btnToggleUnsur');

            function getRankBadgeClass(n) {
                if (n === 1) return 'rank-1';
                if (n === 2) return 'rank-2';
                if (n === 3) return 'rank-3';
                return 'rank-default';
            }

            // Fungsi gabungan filter dan sort
            function applyTableFilterAndSort() {
                const keyword = inputCari ? inputCari.value.toLowerCase().trim() : '';
                const selectedKlaster = selectKlaster ? selectKlaster.value : 'all';
                const activeSortBtn = document.querySelector('#filterUnifiedTable .filter-btn.active');
                const sortMode = activeSortBtn ? activeSortBtn.getAttribute('data-sort') : 'skm-desc';

                // 1. Filter rows
                let visibleRows = allRows.filter(row => {
                    const nama = row.getAttribute('data-nama') || '';
                    const klaster = row.getAttribute('data-klaster') || '';
                    const matchNama = !keyword || nama.includes(keyword);
                    const matchKlaster = selectedKlaster === 'all' || klaster === selectedKlaster;
                    return matchNama && matchKlaster;
                });

                // 2. Sort rows
                visibleRows.sort((a, b) => {
                    if (sortMode === 'skm-desc') {
                        return (parseFloat(b.getAttribute('data-skm')) || 0) - (parseFloat(a.getAttribute('data-skm')) || 0);
                    } else if (sortMode === 'skm-asc') {
                        return (parseFloat(a.getAttribute('data-skm')) || 0) - (parseFloat(b.getAttribute('data-skm')) || 0);
                    } else {
                        return (a.getAttribute('data-nama') || '').localeCompare(b.getAttribute('data-nama') || '');
                    }
                });

                // 3. Re-render rows to tbody
                tbody.innerHTML = '';
                visibleRows.forEach((row, idx) => {
                    tbody.appendChild(row);
                    const badge = row.querySelector('.rank-badge');
                    if (badge) {
                        const originalRank = parseInt(row.getAttribute('data-rank'), 10);
                        const displayedRank = (sortMode === 'nama') ? originalRank : (idx + 1);
                        badge.textContent = displayedRank;
                        badge.className = 'rank-badge ' + getRankBadgeClass(displayedRank);
                    }
                });

                if (visibleRows.length === 0) {
                    const emptyTr = document.createElement('tr');
                    emptyTr.innerHTML = '<td colspan="20" class="text-center py-4 text-muted">Tidak ada unit faskes yang cocok dengan pencarian / filter.</td>';
                    tbody.appendChild(emptyTr);
                }
            }

            // Event Listener Sort
            sortBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    sortBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    applyTableFilterAndSort();
                });
            });

            // Event Listener Filter Klaster & Search
            selectKlaster?.addEventListener('change', applyTableFilterAndSort);
            inputCari?.addEventListener('input', applyTableFilterAndSort);

            // ==========================================
            // 3. TOGGLE KOLOM UNSUR (SHOW / HIDE)
            // ==========================================
            let unsurVisible = true;
            btnToggleUnsur?.addEventListener('click', function () {
                unsurVisible = !unsurVisible;
                const unsurCols = document.querySelectorAll('.col-unsur');
                unsurCols.forEach(col => {
                    col.style.display = unsurVisible ? '' : 'none';
                });

                if (unsurVisible) {
                    this.innerHTML = '<i class="fa-solid fa-eye-slash me-1"></i> Sembunyikan Unsur';
                } else {
                    this.innerHTML = '<i class="fa-solid fa-eye me-1"></i> Tampilkan Unsur (U1-U9)';
                }
            });
        });
    </script>
    @endif
@endsection