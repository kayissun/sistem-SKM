@extends('layouts.dinkes')

@section('title', 'Tinjau Tindak Lanjut')

@section('content')
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
        <a href="{{ route('dinkes.tindak-lanjut.index') }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mt-2">
            <div>
                <h3 style="color:#180733;font-weight:800;">Tinjau Tindak Lanjut</h3>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    <strong>{{ $tindakLanjut->puskesmas->nama ?? '-' }}</strong>
                    &middot; {{ $tindakLanjut->unsurPelayanan->kode ?? '-' }} — {{ $tindakLanjut->unsurPelayanan->nama_unsur ?? '-' }}
                    &middot; TW-{{ $tindakLanjut->triwulan }} {{ $tindakLanjut->tahun }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dinkes.tindak-lanjut.rekap-puskesmas', $tindakLanjut->puskesmas_id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-chart-pie me-1"></i> Rekap Puskesmas
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <!-- Info Tindak Lanjut -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header"><i class="fa-solid fa-info-circle me-2"></i>Detail Tindak Lanjut</div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted fw-semibold" style="width:160px">Status</td>
                            <td><span class="badge {{ $tindakLanjut->status_badge_class }} fs-6">{{ $tindakLanjut->status_label }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Puskesmas/RSU</td>
                            <td class="fw-bold" style="color:#180733;">{{ $tindakLanjut->puskesmas->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Unsur</td>
                            <td>{{ $tindakLanjut->unsurPelayanan->kode ?? '-' }} — {{ $tindakLanjut->unsurPelayanan->nama_unsur ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Triwulan / Tahun</td>
                            <td>Triwulan {{ $tindakLanjut->triwulan }} / {{ $tindakLanjut->tahun }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Nilai Kondisi</td>
                            <td class="fw-bold fs-5">{{ $tindakLanjut->nilai_kondisi !== null ? number_format($tindakLanjut->nilai_kondisi, 1) : '-' }}</td>
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
                                <td class="p-2 rounded" style="background:{{ $tindakLanjut->status === 'rejected' ? '#FEF2F2' : '#ECFDF5' }};">
                                    {!! nl2br(e($tindakLanjut->catatan_dinkes)) !!}
                                </td>
                            </tr>
                        @endif
                        @if ($tindakLanjut->verified_by)
                            <tr>
                                <td class="text-muted fw-semibold">Diverifikasi</td>
                                <td>{{ $tindakLanjut->verifiedBy?->name }} — {{ $tindakLanjut->verified_at?->translatedFormat('d M Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Review Actions -->
            @if ($tindakLanjut->status === 'submitted')
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header" style="background:#ECFDF5;border-bottom:1px solid #A7F3D0;">
                        <i class="fa-solid fa-gavel me-2 text-success"></i>Tinjau & Putuskan
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan untuk Admin Puskesmas</label>
                                <textarea id="catatan-dinkes" class="form-control" rows="3"
                                          placeholder="Berikan catatan atau masukan (wajib jika menolak)..."></textarea>
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <form method="POST" action="{{ route('dinkes.tindak-lanjut.approve', $tindakLanjut) }}" class="d-inline flex-grow-1" id="form-approve">
                                    @csrf
                                    <input type="hidden" name="catatan_dinkes" id="catatan-approve">
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui tindak lanjut ini?')">
                                        <i class="fa-solid fa-circle-check me-1"></i> Setujui
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('dinkes.tindak-lanjut.reject', $tindakLanjut) }}" class="d-inline flex-grow-1" id="form-reject">
                                    @csrf
                                    <input type="hidden" name="catatan_dinkes" id="catatan-reject">
                                    <button type="submit" class="btn btn-danger w-100" onclick="return handleReject(event)">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <!-- Progress Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-timeline me-2"></i>Progres Capaian</span>
                    <span class="badge bg-primary rounded-pill">{{ $tindakLanjut->progress->count() }}</span>
                </div>
                <div class="card-body">
                    @if ($tindakLanjut->progress->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fa-regular fa-clock fa-2x mb-2 d-block opacity-50"></div>
                            Belum ada progres capaian dari admin puskesmas.
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
                                                <div class="mt-1">
                                                    <span class="small text-muted">Nilai:</span>
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

        document.getElementById('form-approve')?.addEventListener('submit', function(e) {
            document.getElementById('catatan-approve').value = document.getElementById('catatan-dinkes').value;
        });

        function handleReject(e) {
            const catatan = document.getElementById('catatan-dinkes').value;
            if (!catatan || catatan.trim().length < 5) {
                e.preventDefault();
                alert('Catatan wajib diisi minimal 5 karakter saat menolak.');
                document.getElementById('catatan-dinkes').focus();
                return false;
            }
            document.getElementById('catatan-reject').value = catatan;
            return confirm('Tolak tindak lanjut ini?');
        }
    </script>
@endsection
