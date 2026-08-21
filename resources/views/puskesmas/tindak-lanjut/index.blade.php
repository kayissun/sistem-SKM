@extends('layouts.puskesmas')

@section('title', 'Tindak Lanjut')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .tl-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
        .tl-head h3 { color:#180733; font-weight:800; margin:0 0 4px; }
        .tl-head p { color:#635C7A; margin:0; font-size:.88rem; }

        /* Unsur Card */
        .unsur-card {
            border-radius: 14px;
            border: 1px solid rgba(109,40,217,.08);
            box-shadow: 0 8px 24px -10px rgba(46,16,101,.12);
            transition: transform .2s, box-shadow .2s;
            overflow: hidden;
            position: relative;
        }
        .unsur-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px -8px rgba(46,16,101,.2); }
        .unsur-card .card-top {
            padding: 16px 18px 12px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .unsur-card .icon-box {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #fff; flex-shrink: 0;
        }
        .unsur-card .info-top h6 { margin: 0; font-weight: 800; font-size: .92rem; color: #180733; }
        .unsur-card .info-top .skor { font-size: 1.5rem; font-weight: 800; margin: 0; }
        .unsur-card .card-body-detail {
            padding: 0 18px 14px;
        }
        .unsur-card .pertanyaan-item {
            background: #F9F7FF;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 6px;
            font-size: .8rem;
            color: #4C1D95;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .unsur-card .pertanyaan-item .num {
            background: #7C3AED;
            color: #fff;
            border-radius: 50%;
            width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: .65rem; font-weight: 700;
            flex-shrink: 0;
        }
        .unsur-card .card-footer-tl {
            padding: 10px 18px;
            background: #FAF8FF;
            border-top: 1px solid rgba(109,40,217,.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .unsur-card .badge-kategori {
            font-weight: 600;
            padding: .3em .7em;
            border-radius: 99px;
            font-size: .7rem;
        }
        .unsur-card .btn-tl {
            border-radius: 8px;
            font-size: .75rem;
            font-weight: 700;
            padding: 5px 12px;
        }

        /* Status colors */
        .skor-a { color: #059669; }
        .skor-b { color: #2563EB; }
        .skor-c { color: #D97706; }
        .skor-d { color: #DC2626; }
        .bg-a { background: linear-gradient(135deg,#059669,#10B981); }
        .bg-b { background: linear-gradient(135deg,#2563EB,#3B82F6); }
        .bg-c { background: linear-gradient(135deg,#D97706,#F59E0B); }
        .bg-d { background: linear-gradient(135deg,#DC2626,#EF4444); }

        .badge-tl { font-weight:600; padding:.35em .7em; border-radius:99px; font-size:.75rem; }
        .section-title { font-weight:800; color:#180733; font-size:1.05rem; margin-bottom:16px; }
    </style>

    <!-- Header -->
    <div class="tl-head">
        <div>
            <h3><i class="fa-solid fa-clipboard-check me-2"></i>Tindak Lanjut</h3>
            <p>Pantau unsur pelayanan yang perlu diperbaiki dan laporkan progres ke Dinkes.</p>
        </div>
        <a href="{{ route('puskesmas.tindak-lanjut.create', ['periode_survei_id' => $periode?->id]) }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Buat Tindak Lanjut
        </a>
    </div>

    <!-- Filter Periode -->
    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted fw-semibold"><i class="fa-solid fa-calendar-days me-1"></i> Periode Survei</label>
                <select name="periode_survei_id" class="form-select" onchange="this.form.submit()">
                    @foreach ($daftarPeriode as $p)
                        <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                            {{ $p->nama }} @if($p->is_active) (Aktif) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold"><i class="fa-solid fa-clock me-1"></i> Triwulan</label>
                <select name="triwulan" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for ($t = 1; $t <= 4; $t++)
                        <option value="{{ $t }}" @selected($triwulan == $t)>Triwulan {{ $t }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold"><i class="fa-solid fa-calendar me-1"></i> Tahun</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </form>

    {{-- ===== BAGIAN ANALISIS 9 UNSUR — CARD KAYA ===== --}}
    @if ($hasil && $hasil['jumlah_responden'] > 0)
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div style="width:4px;height:24px;border-radius:2px;background:linear-gradient(180deg,#7C3AED,#2A0B5E);"></div>
                <h5 class="section-title mb-0">Analisis 9 Unsur Pelayanan</h5>
            </div>
            <p class="text-muted small mb-3" style="font-size:.85rem;">
                Periode <strong>{{ $periode->nama }}</strong> &middot; {{ $hasil['jumlah_responden'] }} responden &middot;
                Nilai IKM <strong>{{ $hasil['nilai_akhir_skm'] }}</strong> — {{ $hasil['mutu_akhir'] }}
            </p>

            @php
                // Icon map untuk setiap kode unsur
                $iconMap = [
                    'U1' => 'fa-file-signature',
                    'U2' => 'fa-hand-holding-heart',
                    'U3' => 'fa-gears',
                    'U4' => 'fa-shield-halved',
                    'U5' => 'fa-headset',
                    'U6' => 'fa-clock-rotate-left',
                    'U7' => 'fa-building-columns',
                    'U8' => 'fa-star',
                    'U9' => 'fa-chart-line',
                ];
            @endphp

            <div class="row g-3">
                @foreach ($hasil['per_unsur'] as $kode => $data)
                    @php
                        $skor = $data['nrr_skala_100'] ?? 0;
                        $kategori = $data['kategori'] ?? '';
                        $jmlPertanyaan = $data['jumlah_pertanyaan_unit'] ?? 0;
                        $totalNilai = $data['total_nilai'] ?? 0;
                        $icon = $iconMap[$kode] ?? 'fa-circle-question';

                        // Status level
                        if ($skor >= 88) { $level = 'a'; $levelLabel = 'Sangat Baik'; }
                        elseif ($skor >= 76) { $level = 'b'; $levelLabel = 'Baik'; }
                        elseif ($skor >= 65) { $level = 'c'; $levelLabel = 'Kurang Baik'; }
                        else { $level = 'd'; $levelLabel = 'Perlu Perbaikan'; }

                        // Extract pertanyaan teks
                        $daftarPertanyaan = [];
                        if (is_array($data['pertanyaan'] ?? null)) {
                            foreach ($data['pertanyaan'] as $pt) {
                                $teks = $pt['teks_pertanyaan'] ?? $pt['pertanyaan'] ?? null;
                                if ($teks) $daftarPertanyaan[] = $teks;
                            }
                        } elseif (!empty($data['pertanyaan'])) {
                            $daftarPertanyaan[] = $data['pertanyaan'];
                        }

                        // Cek apada sudah ada TL untuk unsur ini
                        $sudahAdaTl = $tindakLanjuts->first(fn($tl) => $tl->unsur_pelayanan_id == ($unsurAktif->firstWhere('kode', $kode)?->id));
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card unsur-card h-100">
                            <div class="card-top">
                                <div class="icon-box bg-{{ $level }}">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>
                                <div class="info-top">
                                    <h6>{{ $kode }} — {{ $unsurAktif->firstWhere('kode', $kode)?->nama_unsur ?? $kode }}</h6>
                                    <p class="skor skor-{{ $level }}">{{ number_format($skor, 1) }} <span style="font-size:.7rem;font-weight:600;color:#635C7A;">/ 100</span></p>
                                </div>
                            </div>
                            <div class="card-body-detail">
                                {{-- Pertanyaan yang diukur --}}
                                @if (count($daftarPertanyaan) > 0)
                                    <div class="mb-2" style="font-size:.72rem;font-weight:700;color:#6D28D9;text-transform:uppercase;letter-spacing:.04em;">
                                        <i class="fa-solid fa-list-ol me-1"></i> Pertanyaan yang diukur:
                                    </div>
                                    @foreach (array_slice($daftarPertanyaan, 0, 4) as $idx => $pt)
                                        <div class="pertanyaan-item">
                                            <span class="num">{{ $idx + 1 }}</span>
                                            <span>{{ $pt }}</span>
                                        </div>
                                    @endforeach
                                    @if (count($daftarPertanyaan) > 4)
                                        <div class="small text-muted ms-1">+{{ count($daftarPertanyaan) - 4 }} pertanyaan lainnya</div>
                                    @endif
                                @else
                                    <div class="text-muted small mb-2">
                                        <i class="fa-solid fa-circle-info me-1"></i> {{ $jmlPertanyaan }} pertanyaan aktif
                                    </div>
                                @endif

                                <div class="d-flex gap-2 small text-muted mt-2">
                                    <span><i class="fa-solid fa-users me-1"></i> {{ $data['jumlah_pertanyaan_unit'] ?? 0 }} pertanyaan</span>
                                    <span><i class="fa-solid fa-calculator me-1"></i> NRR: {{ number_format($data['nrr'] ?? 0, 3) }}</span>
                                </div>
                            </div>
                            <div class="card-footer-tl">
                                <span class="badge-kategori badge bg-{{ $level }} bg-opacity-10 text-{{ $level }}">
                                    <i class="fa-solid {{ $level == 'a' ? 'fa-circle-check' : ($level == 'd' ? 'fa-triangle-exclamation' : 'fa-circle-info') }} me-1"></i>
                                    {{ $levelLabel }}
                                </span>
                                @if ($sudahAdaTl)
                                    <span class="small text-success fw-semibold">
                                        <i class="fa-solid fa-circle-check me-1"></i> Sudah ada TL
                                    </span>
                                @else
                                    <a href="{{ route('puskesmas.tindak-lanjut.create', ['periode_survei_id' => $periode?->id, 'unsur' => $kode]) }}"
                                       class="btn btn-outline-primary btn-tl">
                                        <i class="fa-solid fa-plus me-1"></i> Buat TL
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Prioritas --}}
            @if ($peringkatPrioritas->isNotEmpty())
                <div class="card border-0 shadow-sm mt-4" style="background:linear-gradient(135deg,#FFF9EA,#FFFDF5);border-left:4px solid #C88719;">
                    <div class="card-body py-3 px-4">
                        <div class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-ranking-star me-1 text-warning"></i> Ranking Unsur — Prioritas Perbaikan
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach ($peringkatPrioritas as $item)
                                @php
                                    $rSkor = $item['nrr'] * 25;
                                    $rLevel = $rSkor >= 88 ? 'a' : ($rSkor >= 76 ? 'b' : ($rSkor >= 65 ? 'c' : 'd'));
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-{{ $rLevel }} rounded-pill" style="min-width:28px;">{{ $item['peringkat'] }}</span>
                                    <span class="fw-bold" style="font-size:.9rem;">{{ $item['kode'] }}</span>
                                    <span class="text-muted small">NRR: {{ number_format($item['nrr'], 3) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ===== DAFTAR TINDAK LANJUT YANG SUDAH DIBUAT ===== --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-list-check me-2"></i>Daftar Tindak Lanjut</span>
            <span class="badge bg-primary rounded-pill">{{ $tindakLanjuts->count() }} item</span>
        </div>
        <div class="card-body p-0">
            @if ($tindakLanjuts->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                    Belum ada tindak lanjut. Pilih unsur di atas atau klik <strong>Buat Tindak Lanjut</strong>.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-muted" style="width:40px">No</th>
                                <th class="text-muted">Unsur</th>
                                <th class="text-muted">Triwulan</th>
                                <th class="text-muted">Tahun</th>
                                <th class="text-muted">Nilai</th>
                                <th class="text-muted">Status</th>
                                <th class="text-muted">Foto</th>
                                <th class="text-muted">Progres</th>
                                <th class="text-muted" style="width:140px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tindakLanjuts as $i => $tl)
                                @php
                                    $totalProgress = $tl->progress->count();
                                    $tercapaiCount = $tl->progress->where('tercapai', true)->count();
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold" style="color:#180733">
                                            <i class="fa-solid {{ $iconMap[$tl->unsurPelayanan->kode ?? ''] ?? 'fa-circle-question' }} me-1 text-muted"></i>
                                            {{ $tl->unsurPelayanan->kode ?? '-' }}
                                        </div>
                                        <div class="small text-muted">{{ $tl->unsurPelayanan->nama_unsur ?? '-' }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">TW-{{ $tl->triwulan }}</span></td>
                                    <td>{{ $tl->tahun }}</td>
                                    <td class="fw-bold">{{ $tl->nilai_kondisi !== null ? number_format($tl->nilai_kondisi, 1) : '-' }}</td>
                                    <td><span class="badge-tl {{ $tl->status_badge_class }}">{{ $tl->status_label }}</span></td>
                                    <td>
                                        @if ($tl->foto && count($tl->foto) > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="fa-solid fa-camera me-1"></i>{{ count($tl->foto) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($totalProgress > 0)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:6px;">
                                                    <div class="progress-bar bg-success" style="width:{{ ($tercapaiCount / $totalProgress) * 100 }}%"></div>
                                                </div>
                                                <span class="small text-muted">{{ $tercapaiCount }}/{{ $totalProgress }}</span>
                                            </div>
                                        @else
                                            <span class="small text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('puskesmas.tindak-lanjut.show', $tl) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if ($tl->isEditable())
                                                <a href="{{ route('puskesmas.tindak-lanjut.edit', $tl) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                            @endif
                                            @if ($tl->isEditable())
                                                <form method="POST" action="{{ route('puskesmas.tindak-lanjut.submit', $tl) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Kirim ke Dinkes" onclick="return confirm('Kirim tindak lanjut ini ke Dinkes?')">
                                                        <i class="fa-solid fa-paper-plane"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
