@extends('layouts.dinkes')

@section('title', 'Laporan Rekap')

@section('content')

    <style>
        /* ============ Semua warna di bawah HANYA memakai token:
           surface-0 #FFFFFF, surface-1 #FAF8FF, surface-2 #F3EEFF,
           purple-900 #180733, purple-800 #2E1065, purple-700 #6D28D9,
           purple-600 #7C3AED, purple-500 #8B5CF6, purple-100 #EDE9FE,
           ink #14102B, ink-muted #625B78,
           gold-700 #A66A0E, gold-600 #C88719, gold-400 #E4A63B, gold-100 #FCF1DC
           ========================================================= */

        /* ===== Page Header ===== */
        .sp-page-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .sp-page-head .eyebrow {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: .68rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            color: var(--purple-700, #6D28D9); background: var(--purple-100, #EDE9FE);
            padding: 3px 10px; border-radius: 99px; margin-bottom: 8px;
        }
        .sp-page-head h3 { font-weight: 800; color: var(--purple-900, #180733); margin: 0; }
        .sp-page-head p { margin: 2px 0 0; color: var(--ink-muted, #625B78); font-size: .88rem; }

        .sp-export-group { display: flex; gap: 8px; }
        .sp-export-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 15px; border-radius: 10px; font-size: .8rem; font-weight: 600;
            text-decoration: none; border: 1px solid rgba(109,40,217,.18); transition: .15s;
        }
        .sp-export-btn.pdf { background: var(--surface-0, #FFFFFF); color: var(--purple-700, #6D28D9); }
        .sp-export-btn.pdf:hover { background: var(--purple-700, #6D28D9); color: #fff; border-color: var(--purple-700, #6D28D9); }
        .sp-export-btn.excel { background: var(--gold-100, #FCF1DC); color: var(--gold-700, #A66A0E); border-color: #F0DFB2; }
        .sp-export-btn.excel:hover { background: var(--gold-600, #C88719); color: #fff; border-color: var(--gold-600, #C88719); }

        /* ===== Stat Cards ===== */
        .sp-stat-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .sp-stat-card {
            background: var(--surface-0, #FFFFFF);
            border: 1px solid rgba(109,40,217,.12);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(46,16,101,.04);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .sp-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(46,16,101,.08);
        }
        /* Border aksen kiri (Default Ungu) */
        .sp-stat-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--purple-700, #6D28D9);
        }
        /* Border aksen kiri untuk tema Emas */
        .sp-stat-card.gold-theme::before {
            background: var(--gold-600, #C88719);
        }
        .sp-stat-card .icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        /* Icon variasi Ungu */
        .sp-stat-card .icon.purple {
            background: var(--purple-100, #EDE9FE);
            color: var(--purple-700, #6D28D9);
        }
        /* Icon variasi Emas */
        .sp-stat-card .icon.gold {
            background: var(--gold-100, #FCF1DC);
            color: var(--gold-700, #A66A0E);
        }
        .sp-stat-card .label {
            font-size: .72rem;
            font-weight: 700;
            color: var(--ink-muted, #625B78);
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 2px;
        }
        .sp-stat-card .value {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--purple-900, #180733);
            line-height: 1.2;
        }

        /* ===== Filter ===== */
        .sp-filter-card { border-radius: 14px; }
        .sp-filter-card .form-label { font-size: .72rem; letter-spacing: .02em; }
        .sp-filter-card .form-select { border-color: rgba(109,40,217,.15); background: var(--surface-1, #FAF8FF); border-radius: 10px; }
        .sp-filter-card .input-group-text { background: var(--surface-1, #FAF8FF); border-right: none; color: var(--ink-muted, #625B78); border-color: rgba(109,40,217,.15); }
        .sp-filter-card .form-control { border-left: none; background: var(--surface-1, #FAF8FF); border-color: rgba(109,40,217,.15); }
        .sp-filter-card .input-group:focus-within .input-group-text,
        .sp-filter-card .input-group:focus-within .form-control { border-color: #A78BFA; }
        .sp-filter-card .form-select:focus,
        .sp-filter-card .form-control:focus { box-shadow: 0 0 0 .2rem rgba(109,40,217,.12); }
        .sp-filter-card .btn-primary { background: var(--purple-700, #6D28D9); border-color: var(--purple-700, #6D28D9); }
        .sp-filter-card .btn-primary:hover { background: var(--purple-800, #2E1065); border-color: var(--purple-800, #2E1065); }
        .sp-filter-card .btn-outline-secondary { color: var(--ink-muted, #625B78); border-color: rgba(109,40,217,.2); }
        .sp-filter-card .btn-outline-secondary:hover { background: var(--purple-100, #EDE9FE); color: var(--purple-900, #180733); border-color: rgba(109,40,217,.2); }

        /* ===== Empty state ===== */
        .sp-empty-state { border-radius: 16px; border: 1px dashed rgba(109,40,217,.25); background: var(--surface-1, #FAF8FF); }
        .sp-empty-state .icon-wrap {
            width: 60px; height: 60px; border-radius: 16px; margin: 0 auto 12px;
            display: flex; align-items: center; justify-content: center;
            background: var(--purple-100, #EDE9FE); color: var(--purple-700, #6D28D9); font-size: 1.5rem;
        }

        /* ===== Toolbar ===== */
        .sp-table-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px; }
        .sp-table-toolbar .meta { font-size: .8rem; color: var(--ink-muted, #625B78); font-weight: 600; }
        .sp-table-toolbar .meta strong { color: var(--purple-900, #180733); }
        .sp-copy-btn { border-radius: 10px; font-size: .78rem; font-weight: 600; color: var(--purple-700, #6D28D9); border: 1px solid rgba(109,40,217,.2); background: var(--surface-0, #FFFFFF); }
        .sp-copy-btn:hover { background: var(--purple-700, #6D28D9); border-color: var(--purple-700, #6D28D9); color: #fff; }

        /* ===== Bulk bar ===== */
        .sp-bulkbar {
            display: none; align-items: center; gap: 12px;
            background: var(--surface-1, #FAF8FF); border: 1px solid #E4DEF7; border-radius: 12px;
            padding: 10px 16px; margin-bottom: 14px;
        }
        .sp-bulkbar strong { color: var(--purple-900, #180733); }

        /* ===== Table ===== */
        .sp-table-card { border-radius: 14px; overflow: hidden; }
        .sp-table-card .table-responsive { scrollbar-width: thin; scrollbar-color: #C4B5FD transparent; }
        .sp-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 99px; }
        .sp-table-card .table-responsive::-webkit-scrollbar-track { background: transparent; }
        .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
        .sp-table-card thead th {
            position: sticky; top: 0; z-index: 2;
            background: var(--surface-1, #FAF8FF); color: var(--purple-900, #180733);
            font-size: .7rem; letter-spacing: .02em;
            border-bottom: 1px solid rgba(24,7,51,.08);
        }
        .sp-table-card thead th.sp-th-unsur { background: var(--purple-100, #EDE9FE); color: var(--purple-800, #2E1065); }
        .sp-table-card tbody tr { transition: background .15s; }
        .sp-table-card tbody tr:hover { background: var(--surface-1, #FAF8FF); }
        .sp-table-card tbody tr:has(.cek-item:checked) { background: var(--purple-100, #EDE9FE); }
        .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .6rem .85rem; white-space: nowrap; }
        .sp-table-card tbody tr:not(:last-child) td { border-bottom: 1px solid rgba(24,7,51,.05); }

        .sp-rank {
            display: inline-flex; align-items: center; justify-content: center;
            width: 20px; height: 20px; border-radius: 6px;
            background: var(--purple-100, #EDE9FE); color: var(--purple-700, #6D28D9);
            font-size: .66rem; font-weight: 700;
        }
        .sp-ikm { font-weight: 800; color: var(--purple-700, #6D28D9); font-size: .9rem; }
        .sp-nilai-cell { color: var(--ink-muted, #625B78); }

        .sp-badge-soft {
            display: inline-block; font-size: .7rem; font-weight: 600;
            background: var(--surface-2, #F3EEFF); color: var(--ink-muted, #625B78);
            border: 1px solid rgba(24,7,51,.08); border-radius: 8px; padding: 3px 9px;
        }
        .sp-badge-metode {
            display: inline-block; font-size: .7rem; font-weight: 600;
            background: var(--purple-100, #EDE9FE); color: var(--purple-700, #6D28D9);
            border-radius: 8px; padding: 3px 9px;
        }

        .badge-mutu { font-weight: 600; padding: .4em .75em; border-radius: 99px; font-size: .75rem; white-space: nowrap; }
        .badge-mutu-a { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .badge-mutu-b { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
        .badge-mutu-c { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
        .badge-mutu-d { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }

        .sp-chip {
            display: inline-block; font-size: .68rem; font-weight: 600;
            background: var(--gold-100, #FCF1DC); color: var(--gold-700, #A66A0E);
            border: 1px solid #F0DFB2; border-radius: 8px;
            padding: 2px 8px; margin: 2px 4px 2px 0;
        }
        .sp-list-wrap { white-space: normal; min-width: 190px; }

        .sp-icon-btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid rgba(109,40,217,.15);
            background: var(--surface-0, #FFFFFF); color: var(--purple-700, #6D28D9); transition: .15s;
        }
        .sp-icon-btn:hover { background: var(--purple-700, #6D28D9); color: #fff; border-color: var(--purple-700, #6D28D9); }

        input.cek-item, #pilih-semua { cursor: pointer; width: 15px; height: 15px; accent-color: var(--purple-700, #6D28D9); }
    </style>

    <!-- Header -->
    <div class="sp-page-head">
        <div>
            <span class="eyebrow"><i class="fa-solid fa-file-lines"></i> Laporan</span>
            <h3>Laporan Rekap SKM</h3>
            <p>Rekapitulasi nilai Indeks Kepuasan Masyarakat dari seluruh unit pelayanan.</p>
        </div>
        @if ($periode)
            <div class="sp-export-group">
                <a href="{{ route('dinkes.laporan.export-pdf', ['periode_survei_id' => $periode->id]) }}" class="sp-export-btn pdf">
                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('dinkes.laporan.export-excel', ['periode_survei_id' => $periode->id]) }}" class="sp-export-btn excel">
                    <i class="fa-solid fa-file-excel"></i> Export Excel
                </a>
            </div>
        @endif
    </div>

    {{-- Stat Cards dengan Proteksi Safe Variable --}}
    @if ($periode)
        <div class="sp-stat-row">
            {{-- Card 1: Ungu --}}
            <div class="sp-stat-card">
                <div class="icon purple">
                    <i class="fa-solid fa-hospital"></i>
                </div>
                <div>
                    <div class="label">Total Unit</div>
                    <div class="value">{{ $totalUnit ?? count($rekap) }}</div>
                </div>
            </div>

            {{-- Card 2: Emas --}}
            <div class="sp-stat-card gold-theme">
                <div class="icon gold">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="label">Total Responden</div>
                    <div class="value">{{ number_format($totalResponden ?? $rekap->sum('jumlah_responden')) }}</div>
                </div>
            </div>

            {{-- Card 3: Ungu --}}
            <div class="sp-stat-card">
                <div class="icon purple">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <div class="label">Rata-rata IKM</div>
                    <div class="value">{{ $rataRataIkm ?? ($rekap->count() > 0 ? number_format($rekap->avg('nilai_akhir_skm'), 2) : '0') }}</div>
                </div>
            </div>

            {{-- Card 4: Emas --}}
            <div class="sp-stat-card gold-theme">
                <div class="icon gold">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div>
                    <div class="label">IKM Tertinggi</div>
                    <div class="value">{{ $terbaik ?? ($rekap->count() > 0 ? $rekap->max('nilai_akhir_skm') : '0') }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="card sp-filter-card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <!-- Filter Periode Survei -->
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-calendar-days me-1"></i> Periode Survei
                    </label>
                    <select name="periode_survei_id" class="form-select border rounded-3" onchange="this.form.submit()">
                        @foreach ($daftarPeriode as $p)
                            <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                                {{ $p->nama }} @if($p->is_active) (Aktif) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pencarian Unit + Tombol Filter & Reset -->
                <div class="col-md-7">
                    <label class="form-label small text-muted mb-1 fw-semibold">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Pencarian Unit
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="cari" value="{{ $pencarian ?? '' }}" class="form-control rounded-0" placeholder="Cari nama unit pelayanan...">
                        <button class="btn btn-primary fw-medium" type="submit" title="Cari / Filter">
                            Cari
                        </button>
                        <a href="{{ route('dinkes.laporan.index') }}" class="btn btn-outline-secondary fw-medium rounded-end-3" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (!$periode)
        <div class="card sp-empty-state border-0">
            <div class="card-body text-center py-5">
                <div class="icon-wrap"><i class="fa-solid fa-calendar-xmark"></i></div>
                <h6 class="fw-bold mb-1" style="color:var(--purple-900, #180733)">Belum Ada Periode Survei</h6>
                <p class="mb-0 small text-muted">Silakan buat periode survei aktif terlebih dahulu untuk melihat rekapitulasi laporan.</p>
            </div>
        </div>
    @else
        @php
            $barisPertama = $rekap->first();
            // Jika kodeUnsur belum ada dari controller, ekstrak dari data
            if (empty($kodeUnsur) && $barisPertama) {
                $kodeUnsur = array_keys($barisPertama['per_unsur'] ?? []);
            }
            
            // Debug: Log struktur data
            \Log::info('LAPORAN DEBUG', [
                'total_rekap' => count($rekap),
                'kodeUnsur' => $kodeUnsur,
                'first_baris_keys' => $barisPertama ? array_keys($barisPertama) : [],
                'first_baris_per_unsur_keys' => $barisPertama ? array_keys($barisPertama['per_unsur'] ?? []) : [],
            ]);
        @endphp

        <div class="sp-table-toolbar">
            <span class="meta"><strong>{{ count($rekap) }}</strong> unit pelayanan terkap</span>
            <button type="button" class="btn btn-sm sp-copy-btn" onclick="salinTabelKeClipboard('tabel-rekap-gabungan', this)">
                <i class="fa-solid fa-copy me-1"></i> Salin Tabel
            </button>
        </div>
        <!-- DEBUG: kodeUnsur = {{ json_encode($kodeUnsur) }} -->
        <!-- DEBUG: totalKolom = {{ count($kodeUnsur) }} -->

        <div class="card sp-table-card border-0 shadow-sm">
            <div class="table-responsive">
                    <table id="tabel-rekap-gabungan" class="table text-center align-middle mb-0">
                        <thead class="text-uppercase">
                            <tr>
                                <th rowspan="2" class="align-middle" style="width:40px">No</th>
                                <th rowspan="2" class="align-middle text-start">OPD / Unit Pelayanan Publik</th>
                                <th rowspan="2" class="align-middle">Periode Pelaksanaan</th>
                                <th colspan="{{ count($kodeUnsur) }}" class="sp-th-unsur border-bottom">Nilai Per Unsur</th>
                                <th rowspan="2" class="align-middle">IKM</th>
                                <th rowspan="2" class="align-middle">Kategori</th>
                                <th rowspan="2" class="align-middle">Jumlah Responden</th>
                                <th rowspan="2" class="align-middle">Metode SKM</th>
                                <th rowspan="2" class="align-middle">Unsur Prioritas Perbaikan</th>
                                <th rowspan="2" class="align-middle">Rencana Tindak Lanjut</th>
                                <th rowspan="2" class="align-middle" style="width:80px">Aksi</th>
                            </tr>
                            <tr>
                                @forelse ($kodeUnsur as $kode)
                                    @php
                                        // Ekstrak nomor unsur dari kode
                                        $noUnsur = (int) filter_var($kode, FILTER_SANITIZE_NUMBER_INT);
                                        $labelUnsur = ($noUnsur > 0) ? 'U' . $noUnsur : $kode;
                                    @endphp
                                    <th class="sp-th-unsur fs-8" title="{{ $barisPertama['per_unsur'][$kode]['nama_unsur'] ?? $kode }}">{{ $labelUnsur }}</th>
                                @empty
                                    <th class="sp-th-unsur fs-8" colspan="9">Tidak ada unsur</th>
                                @endforelse
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekap as $i => $baris)
                                <tr>
                                    <td><span class="sp-rank">{{ $i + 1 }}</span></td>
                                    <td class="text-start fw-semibold" style="color:var(--purple-900, #180733)">{{ $baris['puskesmas'] }}</td>
                                    <td><span class="sp-badge-soft">{{ $namaPeriodeLengkap ?? $periode?->nama }}</span></td>
                                    @foreach ($kodeUnsur as $kode)
                                        <td class="sp-nilai-cell">
                                            {{ number_format($baris['per_unsur'][$kode]['nrr_skala_100'] ?? 0, 2) }}
                                        </td>
                                    @endforeach
                                    <td class="sp-ikm">{{ $baris['nilai_akhir_skm'] }}</td>
                                    <td>
                                        @php
                                            $mutu = strtolower($baris['mutu_akhir']);
                                            $badgeClass = str_contains($mutu, 'sangat baik') || str_contains($mutu, 'a') ? 'badge-mutu-a' :
                                                          (str_contains($mutu, 'baik') || str_contains($mutu, 'b') ? 'badge-mutu-b' :
                                                          (str_contains($mutu, 'kurang') || str_contains($mutu, 'c') ? 'badge-mutu-c' : 'badge-mutu-d'));
                                        @endphp
                                        <span class="badge-mutu {{ $badgeClass }}">{{ $baris['mutu_akhir'] }}</span>
                                    </td>
                                    <td class="fw-medium">{{ number_format($baris['jumlah_responden']) }}</td>
                                    <td><span class="sp-badge-metode">SKM Online</span></td>
                                    <td class="text-start sp-list-wrap">
                                        @foreach ($baris['unsur_prioritas'] as $prioritas)
                                            <span class="sp-chip">{{ $prioritas }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-start sp-list-wrap text-muted">
                                        @foreach ($baris['rencana_tindak_lanjut'] as $rencana)
                                            <div class="small">{{ $rencana }}</div>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('dinkes.laporan.detail', ['puskesma' => $baris['puskesmas_id']]) }}" 
                                           class="sp-icon-btn" title="Lihat Detail">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 10 + count($kodeUnsur) }}" class="text-center text-muted py-5">
                                        <i class="fa-regular fa-folder-open fa-2x mb-2 d-block" style="color:var(--purple-500, #8B5CF6);opacity:.4"></i>
                                        Belum ada data unit pelayanan aktif pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
    @endif

@endsection