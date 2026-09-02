@extends('layouts.puskesmas')

@section('title', 'Tambah Progres Capaian')

@section('content')
    <div class="mb-4">
        <a href="{{ route('puskesmas.tindak-lanjut.show', $tindakLanjut) }}" class="text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Detail
        </a>
        <h3 class="mt-2" style="color:#180733;font-weight:800;">Tambah Progres Capaian</h3>
        <p class="text-muted" style="font-size:.88rem;">
            {{ $tindakLanjut->unsurPelayanan->kode }} — {{ $tindakLanjut->unsurPelayanan->nama_unsur }}
            &middot; TW-{{ $tindakLanjut->triwulan }} {{ $tindakLanjut->tahun }}
        </p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <form method="POST" action="{{ route('puskesmas.tindak-lanjut.progress.store', $tindakLanjut) }}">
                @csrf
                <div class="card border-0 shadow-sm">
                    <div class="card-header"><i class="fa-solid fa-chart-line me-2"></i>Data Capaian Triwulan</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Triwulan Target <span class="text-danger">*</span></label>
                                <select name="triwulan_target" class="form-select @error('triwulan_target') is-invalid @enderror" required>
                                    @for ($t = 1; $t <= 4; $t++)
                                        <option value="{{ $t }}" @selected(old('triwulan_target', $tindakLanjut->triwulan) == $t)>
                                            TW-{{ $t }}
                                        </option>
                                    @endfor
                                </select>
                                @error('triwulan_target') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tahun Target <span class="text-danger">*</span></label>
                                <select name="tahun_target" class="form-select @error('tahun_target') is-invalid @enderror" required>
                                    @for ($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}" @selected(old('tahun_target', $tindakLanjut->tahun) == $y)>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('tahun_target') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nilai Akhir <span class="text-danger">*</span></label>
                                <input type="number" name="nilai_akhir" value="{{ old('nilai_akhir') }}"
                                       class="form-control @error('nilai_akhir') is-invalid @enderror"
                                       step="0.01" min="0" max="100" required placeholder="Skor NRR skala 100">
                                @error('nilai_akhir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Status Capaian <span class="text-danger">*</span></label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tercapai" value="1"
                                               id="tercapai-ya" @checked(old('tercapai', '1') == '1') required>
                                        <label class="form-check-label fw-semibold" for="tercapai-ya">
                                            <i class="fa-solid fa-circle-check text-gold-emphasis me-1"></i> Tercapai
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tercapai" value="0"
                                               id="tercapai-tidak" @checked(old('tercapai') == '0')>
                                        <label class="form-check-label fw-semibold" for="tercapai-tidak">
                                            <i class="fa-solid fa-circle-xmark text-warning me-1"></i> Belum Tercapai
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                          rows="3" placeholder="Catatan tambahan mengenai capaian...">{{ old('keterangan') }}</textarea>
                                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2 justify-content-end">
                        <a href="{{ route('puskesmas.tindak-lanjut.show', $tindakLanjut) }}" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Simpan Progres
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm" style="background:#FAF8FF;">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-lightbulb text-warning me-1"></i> Catatan</h6>
                    <ul class="small mb-0 text-muted" style="list-style:disc;padding-left:20px;">
                        <li class="mb-1">Isi <strong>Nilai Akhir</strong> dengan skor NRR skala 100 unsur tersebut pada triwulan yang dilaporkan.</li>
                        <li class="mb-1">Pilih <strong>Tercapai</strong> jika nilai sudah membaik, atau <strong>Belum Tercapai</strong> jika masih perlu perbaikan.</li>
                        <li class="mb-0">Progres ini akan terlihat oleh Dinkes saat meninjau laporan Anda.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection