@php
    $tableId = $id ?? 'tabel-responden-' . uniqid();
    $jumlahUnsur = count($kodeUnsur);
    $judul = $judul ?? ($puskesmas->nama ?? '-');
    $perHalaman = $perHalaman ?? 30;
@endphp

<div class="text-center mb-3">
    <h5 class="mb-1">PENGOLAHAN DATA HASIL SURVEY KEPUASAN MASYARAKAT</h5>
    <h6 class="mb-0">PER RESPONDEN DAN PER UNSUR PELAYANAN</h6>
</div>

<table class="table table-borderless table-sm mb-3" style="max-width:520px">
    <tbody>
        <tr>
            <td style="width:150px" class="fw-semibold">Unit Pelayanan</td>
            <td style="width:10px">:</td>
            <td>{{ $judul }}</td>
        </tr>
        <tr>
            <td class="fw-semibold">Periode Survei</td>
            <td>:</td>
            <td>{{ $periode->nama }}</td>
        </tr>
        <tr>
            <td class="fw-semibold">Jumlah Responden</td>
            <td>:</td>
            <td>{{ $hasil['jumlah_responden'] }}</td>
        </tr>
    </tbody>
</table>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
    <form method="GET" class="d-flex align-items-center gap-2">
        <input type="hidden" name="periode_survei_id" value="{{ $periode->id }}">
        <label class="small text-muted mb-0">Tampilkan</label>
        <select name="per_halaman" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            @foreach ([10, 30, 50, 100] as $opsi)
                <option value="{{ $opsi }}" @selected($perHalaman == $opsi)>{{ $opsi }}</option>
            @endforeach
        </select>
        <span class="small text-muted">per halaman</span>
    </form>

    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="salinTabelKeClipboard('{{ $tableId }}', this)">
        Salin Tabel
    </button>
</div>

<div class="table-responsive mb-2">
    <table id="{{ $tableId }}" class="table table-bordered table-sm text-center align-middle bg-white" style="font-size:0.8rem">
        <thead>
            <tr>
                <th rowspan="2" class="align-middle">No. Res</th>
                <th rowspan="2" class="align-middle text-start">Nama</th>
                <th rowspan="2" class="align-middle text-start">Unit yang Dinilai</th>
                <th rowspan="2" class="align-middle">No. HP/WA</th>
                <th rowspan="2" class="align-middle">Umur</th>
                <th rowspan="2" class="align-middle">Pendidikan</th>
                <th rowspan="2" class="align-middle">Pekerjaan</th>
                <th colspan="{{ $jumlahUnsur }}" class="table-warning">Nilai Unsur Pelayanan</th>
            </tr>
            <tr>
                @foreach ($kodeUnsur as $kode)
                    <th class="table-warning">{{ $kode }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($baris as $b)
                <tr>
                    <td>{{ $b['no'] }}</td>
                    <td class="text-start">{{ $b['nama'] }}</td>
                    <td class="text-start">{{ $b['unit_dinilai'] }}</td>
                    <td>{{ $b['no_hp'] }}</td>
                    <td>
                        {{ $b['umur'] ?? '-' }}
                        @if ($b['umur'] !== null)
                            <div class="text-muted" style="font-size:0.7rem">{{ $b['usia_kategori'] }}</div>
                        @endif
                    </td>
                    <td>{{ $b['pendidikan'] ?? '-' }}</td>
                    <td>{{ $b['pekerjaan'] ?? '-' }}</td>
                    @foreach ($kodeUnsur as $kode)
                        <td>{{ $b['nilai'][$kode] ?? '-' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ 7 + $jumlahUnsur }}" class="text-muted">Belum ada responden pada periode ini</td></tr>
            @endforelse
        </tbody>
        @if ($baris->isNotEmpty())
            <tfoot>
                <tr class="table-light">
                    <td colspan="7" class="text-start fw-semibold">&Sigma; Nilai / Unsur</td>
                    @foreach ($kodeUnsur as $kode)
                        <td class="fw-semibold">{{ $hasil['per_unsur'][$kode]['total_nilai'] ?? 0 }}</td>
                    @endforeach
                </tr>
                <tr class="table-light">
                    <td colspan="7" class="text-start fw-semibold">NRR / Unsur</td>
                    @foreach ($kodeUnsur as $kode)
                        <td>{{ number_format($hasil['per_unsur'][$kode]['nrr'] ?? 0, 3) }}</td>
                    @endforeach
                </tr>
                <tr class="table-light">
                    <td colspan="7" class="text-start fw-semibold">NRR Tertimbang / Unsur</td>
                    @foreach ($kodeUnsur as $kode)
                        <td>{{ number_format($hasil['per_unsur'][$kode]['nrr_tertimbang'] ?? 0, 3) }}</td>
                    @endforeach
                </tr>
                <tr class="table-light">
                    <td colspan="7" class="text-start fw-semibold">Kategori per Unsur</td>
                    @foreach ($kodeUnsur as $kode)
                        <td>{{ explode(' ', $hasil['per_unsur'][$kode]['kategori'] ?? '-')[0] }}</td>
                    @endforeach
                </tr>
            </tfoot>
        @endif
    </table>
</div>

<p class="text-muted small">
    Kolom "Umur" diisi angka pasti oleh responden saat mengisi survei, kategori Kemenkes
    di bawahnya (Balita/Remaja/Dewasa/Lansia) dihitung otomatis dari angka tsb.
</p>

@if ($halamanData)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <span class="text-muted small">
            Menampilkan {{ $halamanData->firstItem() ?? 0 }}-{{ $halamanData->lastItem() ?? 0 }}
            dari {{ $halamanData->total() }} responden
        </span>
        {{ $halamanData->links() }}
    </div>
@endif

@if ($baris->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-2">Keterangan</h6>
                    <ul class="small mb-0 ps-3">
                        <li>U1 s.d. U9 = unsur-unsur pelayanan (Permenpan RB 14/2017)</li>
                        <li>NRR = Nilai Rata-rata</li>
                        <li>IKM = Indeks Kepuasan Masyarakat</li>
                        <li>NRR per Unsur = &Sigma; nilai per unsur / jumlah kuesioner terisi</li>
                        <li>NRR Tertimbang = NRR per unsur &times; {{ $jumlahUnsur > 0 ? round(1 / $jumlahUnsur, 3) : 0 }} (1/jumlah unsur)</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-2">Peringkat Prioritas Perbaikan</h6>
                    <table class="table table-sm mb-1">
                        <thead>
                            <tr><th>Unsur</th><th>Rata-rata</th><th style="width:80px">Peringkat</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($peringkat as $p)
                                <tr>
                                    <td>{{ $p['kode'] }} - {{ $p['pertanyaan'] }}</td>
                                    <td>{{ number_format($p['nrr'], 3) }}</td>
                                    <td>{{ $p['peringkat'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="text-muted small mb-0">Peringkat 1 = nilai paling rendah, paling perlu diperbaiki duluan.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-4 align-items-start mb-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">IKM Unit Pelayanan</div>
                <div class="fs-3 fw-bold">{{ $hasil['nilai_akhir_skm'] }}</div>
                <div>Mutu Pelayanan: {{ $hasil['mutu_akhir'] }}</div>
            </div>
        </div>

        <table class="table table-bordered table-sm bg-white mb-0" style="max-width:480px">
            <tbody>
                <tr>
                    <td class="table-warning">A (Sangat Baik)</td><td>88,31 - 100,00</td>
                    <td class="table-warning">C (Kurang Baik)</td><td>65,00 - 76,60</td>
                </tr>
                <tr>
                    <td class="table-warning">B (Baik)</td><td>76,61 - 88,30</td>
                    <td class="table-warning">D (Tidak Baik)</td><td>25,00 - 64,99</td>
                </tr>
            </tbody>
        </table>
    </div>
@endif
