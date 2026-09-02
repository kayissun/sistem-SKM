@extends('layouts.dinkes')

@section('title', 'Detail Laporan - ' . $puskesmas->nama)

@section('content')

    <style>
        /* ===== Color Variables ===== */
        :root {
            --purple-900: #180733;
            --purple-800: #4C1D95;
            --purple-700: #6D28D9;
            --purple-600: #7C3AED;
            --purple-100: #EDE9FE;
            --purple-50:  #FAF8FF;
            
            --gold-700:   #A66A0E;
            --gold-600:   #C88719;
            --gold-500:   #F59E0B;
            --gold-100:   #FCF1DC;
            --gold-50:    #FFFBEB;

            --ink-main:   #1E1B2E;
            --ink-muted:  #625B78;
            --border-color:#E4DEF7;
        }

        /* ===== Header Section ===== */
        .sp-back-link {
            color: var(--ink-muted);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .sp-back-link:hover {
            color: var(--purple-700);
        }

        .sp-page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .sp-page-head h3 {
            font-weight: 800;
            color: var(--purple-900);
            margin: 0;
            font-size: 1.6rem;
            letter-spacing: -0.02em;
        }
        .sp-page-head p {
            margin: 4px 0 0;
            color: var(--ink-muted);
            font-size: 0.9rem;
        }

        /* ===== Action Buttons ===== */
        .btn-purple-primary {
            background-color: var(--purple-700);
            color: #fff;
            border: none;
            font-weight: 600;
        }
        .btn-purple-primary:hover {
            background-color: var(--purple-800);
            color: #fff;
        }
        
        .btn-gold-action {
            background-color: var(--gold-500);
            color: #fff;
            border: none;
            font-weight: 600;
        }
        .btn-gold-action:hover {
            background-color: var(--gold-600);
            color: #fff;
        }

        .btn-outline-purple {
            border: 1px solid var(--purple-700);
            color: var(--purple-700);
            background: transparent;
            font-weight: 600;
        }
        .btn-outline-purple:hover {
            background: var(--purple-100);
            color: var(--purple-800);
        }

        /* ===== SKM Highlight Card (Ungu-Emas Gradient) ===== */
        .sp-skm-card {
            background: linear-gradient(135deg, var(--purple-900) 0%, var(--purple-700) 60%, var(--gold-600) 100%);
            color: #fff;
            border-radius: 18px;
            padding: 28px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -5px rgba(109, 40, 217, 0.3);
        }
        .sp-skm-card::after {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -20px;
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }
        .sp-skm-card .card-title-sub {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 700;
        }
        .sp-skm-card .score-display {
            font-size: 3.2rem;
            font-weight: 900;
            line-height: 1;
            margin: 10px 0;
            letter-spacing: -0.03em;
            text-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .sp-badge-gold {
            background: var(--gold-500);
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 6px 16px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(200, 135, 25, 0.4);
        }

        /* ===== Section Titles ===== */
        .sp-section-title {
            font-weight: 800;
            color: var(--purple-900);
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        .sp-section-title i {
            color: var(--purple-700);
        }

        /* ===== Table Card Styling ===== */
        .sp-table-card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 12px rgba(24, 7, 51, 0.03);
        }
        .sp-table-card .table {
            margin-bottom: 0;
            font-size: 0.88rem;
        }
        .sp-table-card table thead th {
            background-color: var(--purple-50);
            color: var(--purple-900);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
        }
        .sp-table-card tbody tr:hover {
            background-color: var(--purple-50);
        }

        /* ===== Accordion Styling ===== */
        .sp-accordion .accordion-item {
            border: 1px solid var(--border-color) !important;
            border-radius: 14px !important;
            overflow: hidden;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(24, 7, 51, 0.02);
        }
        .sp-accordion .accordion-button {
            font-weight: 700;
            color: var(--purple-900);
            background: #fff;
            padding: 16px 20px;
            box-shadow: none !important;
        }
        .sp-accordion .accordion-button:not(.collapsed) {
            background-color: var(--purple-50);
            color: var(--purple-700);
            border-bottom: 1px solid var(--border-color);
        }
        .sp-accordion .accordion-button::after {
            filter: opacity(0.6);
        }
        .sp-accordion .accordion-button:not(.collapsed)::after {
            filter: drop-shadow(0px 0px 1px var(--purple-700));
        }

        /* ===== Custom Alert ===== */
        .sp-alert-gold {
            background-color: var(--gold-50);
            border: 1px solid rgba(200, 135, 25, 0.3);
            border-radius: 14px;
            color: var(--purple-900);
        }
    </style>

    <div class="mb-3">
        <a href="{{ route('dinkes.laporan.index', ['periode_survei_id' => $periode->id]) }}" class="sp-back-link">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Rekap Laporan
        </a>
    </div>

    <div class="sp-page-head">
        <div>
            <h3>{{ $puskesmas->nama }}</h3>
            <p>
                Periode: <strong>{{ $periode->nama }}</strong> &middot; 
                Total Responden: <strong class="text-purple-700">{{ number_format($hasil['jumlah_responden']) }}</strong> orang
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dinkes.laporan.detail.export-pdf', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('dinkes.laporan.detail.export-excel', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-success btn-sm rounded-3 px-3">
                <i class="fa-solid fa-file-excel me-1"></i> Excel
            </a>
            <a href="{{ route('dinkes.laporan.data-responden', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-outline-purple btn-sm rounded-3 px-3">
                <i class="fa-solid fa-users me-1"></i> Responden
            </a>
            <a href="{{ route('dinkes.laporan.publikasi', ['puskesma' => $puskesmas, 'periode_survei_id' => $periode->id]) }}" class="btn btn-purple-primary btn-sm rounded-3 px-3 shadow-sm">
                <i class="fa-solid fa-bullhorn me-1"></i> Format Publikasi
            </a>
        </div>
    </div>

    @if (!empty($hasil['unsur_belum_terpetakan']))
        <div class="sp-alert-gold p-3 mb-4 d-flex gap-3 align-items-start">
            <i class="fa-solid fa-triangle-exclamation fa-lg mt-2 text-warning"></i>
            <div>
                <strong class="text-dark">Perhatian:</strong> Unit ini belum memiliki pertanyaan aktif untuk unsur berikut. Nilai SKM mungkin kurang akurat hingga pertanyaan ditambahkan:
                <ul class="mb-0 mt-1 ps-3 fw-medium">
                    @foreach ($hasil['unsur_belum_terpetakan'] as $unsur)
                        <li>{{ $unsur }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6 col-lg-5">
            <div class="sp-skm-card">
                <div class="card-title-sub">Nilai Akhir SKM (Seluruh Layanan)</div>
                <div class="score-display">{{ $hasil['nilai_akhir_skm'] }}</div>
                <div class="d-flex align-items-center gap-3">
                    <span class="sp-badge-gold">
                        <i class="fa-solid fa-award"></i> {{ $hasil['mutu_akhir'] }}
                    </span>
                    <span class="small opacity-75">
                        <i class="fa-solid fa-users me-1"></i> {{ number_format($hasil['jumlah_responden']) }} Responden
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="sp-section-title">
            <i class="fa-solid fa-chart-pie"></i> Indeks Kepuasan Masyarakat — Seluruh Layanan
        </div>
        <div class="card sp-table-card">
            <div class="card-body p-0">
                @include('partials.matriks-skm', ['hasil' => $hasil, 'judul' => $puskesmas->nama, 'id' => 'tabel-seluruh-layanan'])
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="sp-section-title">
            <i class="fa-solid fa-hospital-user"></i> IKM per Poli / Unit Layanan
        </div>
        
        @if ($hasilPerPoli->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted">
                Belum ada poli / unit layanan terdaftar pada unit ini.
            </div>
        @else
            <div class="accordion sp-accordion" id="accordionPoliDinkes">
                @foreach ($hasilPerPoli as $i => $poli)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#poliDinkes{{ $i }}">
                                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                    <span>{{ $poli['unit_layanan_nama'] }}</span>
                                    <span class="badge bg-light text-dark font-normal border">
                                        {{ $poli['jumlah_responden'] }} responden
                                        @if ($poli['jumlah_responden'] > 0)
                                            &middot; <strong class="text-purple-700">IKM {{ $poli['nilai_akhir_skm'] }}</strong> ({{ $poli['mutu_akhir'] }})
                                        @endif
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="poliDinkes{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#accordionPoliDinkes">
                            <div class="accordion-body p-3">
                                @include('partials.matriks-skm', ['hasil' => $poli, 'judul' => $poli['unit_layanan_nama'], 'id' => 'tabel-poli-dinkes-' . $i])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if (!empty($hasil['pertanyaan_tambahan']))
        <div class="mb-4">
            <div class="sp-section-title">
                <i class="fa-solid fa-comments"></i> Pertanyaan Tambahan
            </div>
            <div class="card sp-table-card">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Pertanyaan</th>
                                <th style="width:100px">Tipe</th>
                                <th style="width:140px">Jumlah Jawaban</th>
                                <th style="width:120px">Rata-rata</th>
                                <th style="width:130px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil['pertanyaan_tambahan'] as $tambahan)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $tambahan['teks_pertanyaan'] }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $tambahan['tipe_input'] === 'teks' ? 'Teks' : 'Skala' }}</span></td>
                                    <td>{{ $tambahan['jumlah_jawaban'] }}</td>
                                    <td>{{ $tambahan['rata_rata'] ?? '-' }}</td>
                                    <td class="text-center">
                                        @if ($tambahan['tipe_input'] === 'teks' && $tambahan['jumlah_jawaban'] > 0)
                                            <a href="{{ route('dinkes.laporan.jawaban-teks', ['puskesma' => $puskesmas, 'pertanyaan' => $tambahan['id'], 'periode_survei_id' => $periode->id]) }}"
                                               class="btn btn-sm btn-outline-purple rounded-3">Lihat Jawaban</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection