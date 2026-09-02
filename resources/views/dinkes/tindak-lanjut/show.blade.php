@extends('layouts.dinkes')

@section('title', 'Tinjau Tindak Lanjut')

@section('content')
    <style>
        /* ============ COLOR TOKENS ============ */
        :root {
            --purple-900: #180733;
            --purple-800: #2E1065;
            --purple-700: #6D28D9;
            --purple-100: #EDE9FE;
            --surface-0: #FFFFFF;
            --surface-1: #FAF8FF;
            --surface-2: #F3EEFF;
            --ink-muted: #625B78;
        }

        /* ============ BACK LINK ============ */
        .back-link { 
            display:inline-flex; align-items:center; gap:6px; color:var(--purple-700); 
            text-decoration:none; font-weight:600; font-size:.9rem; transition:.15s;
        }
        .back-link:hover { color:var(--purple-800); }

        /* ============ PAGE HEAD ============ */
        .page-head { margin-bottom:24px; }
        .page-head h3 { 
            color:var(--purple-900); font-weight:800; margin:8px 0 4px; font-size:1.75rem;
        }
        .page-head p { 
            color:var(--ink-muted); margin:0; font-size:.88rem;
        }

        /* ============ CARDS ============ */
        .detail-card {
            border:none; border-radius:14px; box-shadow:0 2px 6px rgba(46,16,101,.04);
            overflow:hidden; transition:.15s;
        }
        .detail-card:hover { box-shadow:0 6px 16px rgba(46,16,101,.08); }
        .detail-card .card-header {
            background:var(--surface-1); border-bottom:2px solid rgba(109,40,217,.1);
            padding:1rem; color:var(--purple-900); font-weight:700;
        }
        .detail-card .card-body { padding:1.25rem; }

        /* ============ DETAIL TABLE ============ */
        .detail-table { margin-bottom:0; }
        .detail-table td { padding:12px 0; border-bottom:1px solid rgba(109,40,217,.05); }
        .detail-table td:last-child { border-bottom:none; }
        .detail-table td:first-child { 
            font-weight:700; color:var(--ink-muted); width:160px;
        }
        .detail-table td:last-child { 
            color:var(--purple-900);
        }

        /* ============ BADGES ============ */
        .badge-status {
            display:inline-block; padding:6px 12px; border-radius:8px; 
            font-weight:600; font-size:.8rem;
        }
        .badge-status.submitted {
            background:#DCFCE7; color:#166534;
        }
        .badge-status.draft {
            background:#FEF3C7; color:#854D0E;
        }
        .badge-status.tercapai {
            background:#DCFCE7; color:#166534;
        }
        .badge-status.belum {
            background:#FEF3C7; color:#854D0E;
        }

        /* ============ TIMELINE ============ */
        .tl-timeline { position:relative; padding-left:32px; }
        .tl-timeline::before { 
            content:''; position:absolute; left:11px; top:0; bottom:0; 
            width:2px; background:rgba(109,40,217,.1);
        }
        .tl-timeline .tl-item { position:relative; margin-bottom:20px; }
        .tl-timeline .tl-dot { 
            position:absolute; left:-26px; top:4px; width:14px; height:14px; 
            border-radius:50%; border:3px solid var(--surface-0); 
        }
        .tl-timeline .tl-dot.tercapai { 
            background:#10B981;
        }
        .tl-timeline .tl-dot.belum { 
            background:#F59E0B;
        }
        .tl-timeline .tl-card {
            border:none; border-radius:12px; box-shadow:0 2px 6px rgba(46,16,101,.04);
        }
        .tl-timeline .tl-card.tercapai {
            background:linear-gradient(135deg, #ECFDF5, #F0FDF4);
        }
        .tl-timeline .tl-card.belum {
            background:linear-gradient(135deg, #FFFBEB, #FEF3C7);
        }
        .tl-timeline .tl-card .card-body { padding:12px 16px; }

        /* ============ FOTO GRID ============ */
        .foto-grid { display:flex; flex-wrap:wrap; gap:12px; }
        .foto-grid img {
            width:100px; height:100px; object-fit:cover;
            border-radius:10px; border:2px solid rgba(109,40,217,.1);
            cursor:pointer; transition:.2s;
        }
        .foto-grid img:hover { 
            transform:scale(1.05); border-color:var(--purple-700);
            box-shadow:0 4px 12px rgba(109,40,217,.15);
        }

        /* ============ MODAL ============ */
        .foto-modal-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,.9); z-index:9999;
            align-items:center; justify-content:center;
            animation:fadeIn .2s;
        }
        .foto-modal-overlay.active { display:flex; }
        .foto-modal-overlay img { 
            max-width:90vw; max-height:90vh; border-radius:12px;
            box-shadow:0 20px 60px rgba(0,0,0,.3);
        }
        .foto-modal-close {
            position:absolute; top:20px; right:20px;
            color:#fff; font-size:2rem; cursor:pointer; transition:.15s;
        }
        .foto-modal-close:hover { opacity:.7; }

        @keyframes fadeIn {
            from { opacity:0; }
            to { opacity:1; }
        }
    </style>

    <!-- Page Header -->
    <div class="page-head">
        <a href="{{ route('dinkes.tindak-lanjut.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <h3>Tinjau Tindak Lanjut</h3>
        <p>
            <strong>{{ $tindakLanjut->puskesmas->nama ?? '-' }}</strong>
            &middot; {{ $tindakLanjut->unsurPelayanan->kode ?? '-' }} 
            &middot; TW-{{ $tindakLanjut->triwulan }} {{ $tindakLanjut->tahun }}
        </p>
    </div>

    <div class="row g-4">
        <!-- Detail Section -->
        <div class="col-lg-7">
            <div class="detail-card">
                <div class="card-header">
                    <i class="fa-solid fa-clipboard me-2" style="color:var(--purple-700)"></i>
                    Detail Tindak Lanjut
                </div>
                <div class="card-body">
                    <table class="table detail-table">
                        <tr>
                            <td>Status</td>
                            <td>
                                <span class="badge-status {{ $tindakLanjut->status === 'submitted' ? 'submitted' : 'draft' }}">
                                    {{ $tindakLanjut->status_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Puskesmas/RSU</td>
                            <td class="fw-bold" style="font-size:1.05rem">{{ $tindakLanjut->puskesmas->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Unsur Pelayanan</td>
                            <td>{{ $tindakLanjut->unsurPelayanan->kode ?? '-' }} — {{ $tindakLanjut->unsurPelayanan->nama_unsur ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Periode</td>
                            <td>Triwulan {{ $tindakLanjut->triwulan }} / {{ $tindakLanjut->tahun }}</td>
                        </tr>
                        <tr>
                            <td>Nilai Kondisi</td>
                            <td>
                                <span class="fw-bold fs-5">
                                    {{ $tindakLanjut->nilai_kondisi !== null ? number_format($tindakLanjut->nilai_kondisi, 1) : '-' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Tindakan Perbaikan</td>
                            <td>{!! nl2br(e($tindakLanjut->tindakan_perbaikan)) !!}</td>
                        </tr>
                        @if ($tindakLanjut->bukti)
                            <tr>
                                <td>Bukti Pendukung</td>
                                <td>{!! nl2br(e($tindakLanjut->bukti)) !!}</td>
                            </tr>
                        @endif
                        @if ($tindakLanjut->foto && count($tindakLanjut->foto) > 0)
                            <tr>
                                <td><i class="fa-solid fa-images me-1" style="color:var(--purple-700)"></i> Foto Bukti</td>
                                <td>
                                    <div class="foto-grid">
                                        @foreach ($tindakLanjut->foto as $path)
                                            <img src="{{ asset('storage/' . $path) }}" alt="Foto bukti" 
                                                 onclick="bukaFotoModal('{{ asset('storage/' . $path) }}')">
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Progress Section -->
        <div class="col-lg-5">
            <div class="detail-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fa-solid fa-timeline me-2" style="color:var(--purple-700)"></i>
                        Progres Capaian
                    </span>
                    <span style="background:var(--purple-700); color:#fff; padding:4px 10px; border-radius:6px; font-size:.75rem; font-weight:600">
                        {{ $tindakLanjut->progress->count() }}
                    </span>
                </div>
                <div class="card-body">
                    @if ($tindakLanjut->progress->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fa-regular fa-clock fa-2x mb-3 d-block opacity-50"></i>
                            <div class="small">Belum ada progres capaian</div>
                        </div>
                    @else
                        <div class="tl-timeline">
                            @foreach ($tindakLanjut->progress->sortByDesc('tahun_target')->sortByDesc('triwulan_target') as $prog)
                                <div class="tl-item">
                                    <div class="tl-dot {{ $prog->tercapai ? 'tercapai' : 'belum' }}"></div>
                                    <div class="tl-card {{ $prog->tercapai ? 'tercapai' : 'belum' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="fw-bold" style="color:var(--purple-900)">
                                                        TW-{{ $prog->triwulan_target }} {{ $prog->tahun_target }}
                                                    </span>
                                                    @if ($prog->nilai_akhir !== null)
                                                        <span class="ms-2 badge-status {{ $prog->tercapai ? 'tercapai' : 'belum' }}">
                                                            {{ $prog->tercapai ? '✓ Tercapai' : '✕ Belum Tercapai' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($prog->nilai_akhir !== null)
                                                <div class="small mb-2">
                                                    <span style="color:var(--ink-muted)">Nilai Target:</span>
                                                    <span class="fw-bold" style="color:var(--purple-900)">{{ number_format($prog->nilai_akhir, 1) }}</span>
                                                </div>
                                            @endif
                                            @if ($prog->keterangan)
                                                <div class="small" style="color:var(--ink-muted)">{!! nl2br(e($prog->keterangan)) !!}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Foto Modal -->
    <div class="foto-modal-overlay" id="fotoModal" onclick="tutupFotoModal()">
        <span class="foto-modal-close" onclick="tutupFotoModal(event)">
            <i class="fa-solid fa-xmark"></i>
        </span>
        <img src="" alt="Foto Detail" id="fotoModalImg">
    </div>

    <script>
        function bukaFotoModal(src) {
            document.getElementById('fotoModalImg').src = src;
            document.getElementById('fotoModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function tutupFotoModal(event) {
            if (event) event.stopPropagation();
            document.getElementById('fotoModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') tutupFotoModal();
        });
    </script>
@endsection