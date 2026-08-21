@extends('layouts.puskesmas')

@section('title', 'Detail Tindak Lanjut')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .tl-timeline { position:relative; padding-left:32px; }
        .tl-timeline::before { content:''; position:absolute; left:11px; top:0; bottom:0; width:2px; background:#E4DEF7; }
        .tl-timeline .tl-item { position:relative; margin-bottom:20px; }
        .tl-timeline .tl-dot { position:absolute; left:-26px; top:4px; width:14px; height:14px; border-radius:50%; border:3px solid #fff; box-shadow:0 0 0 2px #E4DEF7; }
        .tl-timeline .tl-dot.tercapai { background:#10B981; box-shadow:0 0 0 2px #10B981; }
        .tl-timeline .tl-dot.belum { background:#F59E0B; box-shadow:0 0 0 2px #F59E0B; }
        .foto-grid { display:flex; flex-wrap:wrap; gap:10px; }
        .foto-grid img {
            width:100px; height:100px; object-fit:cover;
            border-radius:10px; border:2px solid #E4DEF7;
            cursor:pointer; transition: .2s;
        }
        .foto-grid img:hover { transform:scale(1.05); border-color:#7C3AED; }
        .foto-modal-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,.85); z-index:9999;
            align-items:center; justify-content:center;
        }
        .foto-modal-overlay.active { display:flex; }
        .foto-modal-overlay img { max-width:90vw; max-height:90vh; border-radius:12px; }
        .foto-modal-close {
            position:absolute; top:20px; right:20px;
            color:#fff; font-size:2rem; cursor:pointer;
        }
    </style>

    <div class="mb-4">
        <a href="{{ route('puskesmas.tindak-lanjut.index') }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mt-2">
            <div>
                <h3 style="color:#180733;font-weight:800;">Detail Tindak Lanjut</h3>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    {{ $tindakLanjut->unsurPelayanan->kode }} — {{ $tindakLanjut->unsurPelayanan->nama_unsur }}
                    &middot; TW-{{ $tindakLanjut->triwulan }} {{ $tindakLanjut->tahun }}
                </p>
            </div>
            <div class="d-flex gap-2">
                @if ($tindakLanjut->isEditable())
                    <a href="{{ route('puskesmas.tindak-lanjut.edit', $tindakLanjut) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-pen me-1"></i> Edit
                    </a>
                @endif
                @if ($tindakLanjut->isEditable())
                    <form method="POST" action="{{ route('puskesmas.tindak-lanjut.submit', $tindakLanjut) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Kirim ke Dinkes?')">
                            <i class="fa-solid fa-paper-plane me-1"></i> Kirim ke Dinkes
                        </button>
                    </form>
                @endif
                <a href="{{ route('puskesmas.tindak-lanjut.progress.create', $tindakLanjut) }}" class="btn btn-outline-success btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Progres
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header"><i class="fa-solid fa-info-circle me-2"></i>Informasi Tindak Lanjut</div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted fw-semibold" style="width:160px">Status</td>
                            <td><span class="badge {{ $tindakLanjut->status_badge_class }}">{{ $tindakLanjut->status_label }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Unsur</td>
                            <td>{{ $tindakLanjut->unsurPelayanan->kode }} — {{ $tindakLanjut->unsurPelayanan->nama_unsur }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Triwulan / Tahun</td>
                            <td>Triwulan {{ $tindakLanjut->triwulan }} / {{ $tindakLanjut->tahun }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Nilai Kondisi</td>
                            <td class="fw-bold">{{ $tindakLanjut->nilai_kondisi !== null ? number_format($tindakLanjut->nilai_kondisi, 1) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Tindakan Perbaikan</td>
                            <td>{!! nl2br(e($tindakLanjut->tindakan_perbaikan)) !!}</td>
                        </tr>
                        @if ($tindakLanjut->bukti)
                            <tr>
                                <td class="text-muted fw-semibold">Bukti (Teks)</td>
                                <td>{!! nl2br(e($tindakLanjut->bukti)) !!}</td>
                            </tr>
                        @endif
                        @if ($tindakLanjut->foto && count($tindakLanjut->foto) > 0)
                            <tr>
                                <td class="text-muted fw-semibold"><i class="fa-solid fa-camera me-1"></i> Foto Bukti</td>
                                <td>
                                    <div class="foto-grid">
                                        @foreach ($tindakLanjut->foto as $path)
                                            <img src="{{ asset('storage/' . $path) }}" alt="Foto bukti" onclick="bukaFotoModal('{{ asset('storage/' . $path) }}')">
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @if ($tindakLanjut->catatan_dinkes)
                            <tr>
                                <td class="text-muted fw-semibold">Catatan Dinkes</td>
                                <td class="p-2 rounded" style="background:#FEF2F2;">{!! nl2br(e($tindakLanjut->catatan_dinkes)) !!}</td>
                            </tr>
                        @endif
                        @if ($tindakLanjut->verified_by)
                            <tr>
                                <td class="text-muted fw-semibold">Diverifikasi oleh</td>
                                <td>{{ $tindakLanjut->verifiedBy?->name }} — {{ $tindakLanjut->verified_at?->translatedFormat('d M Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-timeline me-2"></i>Progres Capaian</span>
                    <span class="badge bg-primary rounded-pill">{{ $tindakLanjut->progress->count() }}</span>
                </div>
                <div class="card-body">
                    @if ($tindakLanjut->progress->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fa-regular fa-clock fa-2x mb-2 d-block opacity-50"></div>
                            Belum ada progres capaian.
                            <br>
                            <a href="{{ route('puskesmas.tindak-lanjut.progress.create', $tindakLanjut) }}" class="btn btn-sm btn-outline-primary mt-2">
                                Tambah Progres
                            </a>
                        </div>
                    @else
                        <div class="tl-timeline">
                            @foreach ($tindakLanjut->progress->sortByDesc('triwulan_target')->sortByDesc('tahun_target') as $prog)
                                <div class="tl-item">
                                    <div class="tl-dot {{ $prog->tercapai ? 'tercapai' : 'belum' }}"></div>
                                    <div class="card border-0 shadow-sm" style="background:{{ $prog->tercapai ? '#ECFDF5' : '#FFFBEB' }}">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <span class="fw-bold" style="color:#180733;">
                                                        TW-{{ $prog->triwulan_target }} {{ $prog->tahun_target }}
                                                    </span>
                                                    @if ($prog->nilai_akhir !== null)
                                                        <span class="ms-2 badge {{ $prog->tercapai ? 'bg-success' : 'bg-warning text-dark' }}">
                                                            {{ $prog->tercapai ? 'Tercapai' : 'Belum Tercapai' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($prog->nilai_akhir !== null)
                                                <div class="mt-1 small">
                                                    <span class="text-muted">Nilai:</span>
                                                    <span class="fw-bold">{{ number_format($prog->nilai_akhir, 1) }}</span>
                                                </div>
                                            @endif
                                            @if ($prog->keterangan)
                                                <div class="mt-1 small text-muted">{!! nl2br(e($prog->keterangan)) !!}</div>
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

    {{-- Foto Modal --}}
    <div class="foto-modal-overlay" id="fotoModal" onclick="tutupFotoModal()">
        <span class="foto-modal-close" onclick="tutupFotoModal()"><i class="fa-solid fa-xmark"></i></span>
        <img src="" alt="Foto Detail" id="fotoModalImg">
    </div>

    <script>
        function bukaFotoModal(src) {
            document.getElementById('fotoModalImg').src = src;
            document.getElementById('fotoModal').classList.add('active');
        }
        function tutupFotoModal() {
            document.getElementById('fotoModal').classList.remove('active');
        }
    </script>
@endsection
