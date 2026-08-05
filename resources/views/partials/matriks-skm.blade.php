@php
    $jumlahUnsur = count($hasil['per_unsur']);
    $bobot = $jumlahUnsur > 0 ? round(1 / $jumlahUnsur, 2) : 0;
    $tableId = $id ?? 'tabel-skm-' . uniqid();
@endphp

<div class="mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h6 class="mb-0">{{ $judul ?? 'Seluruh Layanan' }}</h6>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small">Jumlah kuesioner terisi: {{ $hasil['jumlah_responden'] }}</span>
        @if ($hasil['jumlah_responden'] > 0)
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="salinTabelKeClipboard('{{ $tableId }}', this)">
                Salin Tabel
            </button>
        @endif
    </div>
</div>

@if ($hasil['jumlah_responden'] === 0)
    <p class="text-muted small mb-4">Belum ada data pada periode ini.</p>
@else
    <div class="table-responsive mb-4">
        <table id="{{ $tableId }}" class="table table-bordered text-center align-middle bg-white mb-0" style="font-size:0.85rem">
            <thead>
                <tr>
                    <th rowspan="2" class="text-start align-middle" style="min-width:220px">Uraian</th>
                    <th colspan="{{ $jumlahUnsur }}" class="table-warning">Nilai Unsur Pelayanan</th>
                </tr>
                <tr>
                    @foreach ($hasil['per_unsur'] as $kode => $u)
                        <th class="table-warning">{{ $kode }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-start">Total Nilai Per Unsur</td>
                    @foreach ($hasil['per_unsur'] as $u)
                        <td>{{ $u['total_nilai'] }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="text-start">IKM per unsur = Total Nilai Per Unsur / Jumlah Kuesioner Terisi</td>
                    @foreach ($hasil['per_unsur'] as $u)
                        <td>{{ number_format($u['nrr'], 3) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="text-start">Nilai Interval Konversi = IKM per unsur x 25</td>
                    @foreach ($hasil['per_unsur'] as $u)
                        <td>{{ number_format($u['nrr_skala_100'], 3) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="text-start">Mutu Layanan</td>
                    @foreach ($hasil['per_unsur'] as $u)
                        <td>{{ explode(' ', $u['kategori'])[0] }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="text-start">NRR Tertimbang Per Unsur = IKM per unsur x {{ $bobot }}</td>
                    @foreach ($hasil['per_unsur'] as $u)
                        <td>{{ number_format($u['nrr_tertimbang'], 3) }}</td>
                    @endforeach
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td class="text-start fw-semibold">Jumlah NRR Tertimbang</td>
                    <td colspan="{{ $jumlahUnsur }}" class="fw-semibold">{{ number_format($hasil['total_indeks_skm'], 3) }}</td>
                </tr>
                <tr class="table-light">
                    <td class="text-start fw-semibold">Nilai Indeks Kepuasan Masyarakat (IKM) = Jumlah NRR Tertimbang x 25</td>
                    <td colspan="{{ $jumlahUnsur }}" class="fw-semibold">{{ number_format($hasil['nilai_akhir_skm'], 3) }} ({{ $hasil['mutu_akhir'] }})</td>
                </tr>
            </tfoot>
        </table>
    </div>
@endif
