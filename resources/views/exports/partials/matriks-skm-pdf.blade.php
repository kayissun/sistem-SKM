@php
    $jumlahUnsur = count($hasil['per_unsur']);
    $bobot = $jumlahUnsur > 0 ? round(1 / $jumlahUnsur, 2) : 0;
@endphp

<p style="margin:2px 0"><strong>{{ $hasil['unit_layanan_nama'] ?? $hasil['puskesmas'] ?? '' }}</strong> &middot; Jumlah kuesioner terisi: {{ $hasil['jumlah_responden'] }}</p>

@if ($hasil['jumlah_responden'] === 0)
    <p style="color:#888">Belum ada data pada periode ini.</p>
@else
    <table>
        <thead>
            <tr>
                <th class="label" style="width:200px">Uraian</th>
                @foreach ($hasil['per_unsur'] as $kode => $u)
                    <th>{{ $kode }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Total Nilai Per Unsur</td>
                @foreach ($hasil['per_unsur'] as $u)
                    <td>{{ $u['total_nilai'] }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="label">IKM per unsur</td>
                @foreach ($hasil['per_unsur'] as $u)
                    <td>{{ number_format($u['nrr'], 3) }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="label">Nilai Interval Konversi (x25)</td>
                @foreach ($hasil['per_unsur'] as $u)
                    <td>{{ number_format($u['nrr_skala_100'], 3) }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="label">Mutu Layanan</td>
                @foreach ($hasil['per_unsur'] as $u)
                    <td>{{ explode(' ', $u['kategori'])[0] }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="label">NRR Tertimbang (x{{ $bobot }})</td>
                @foreach ($hasil['per_unsur'] as $u)
                    <td>{{ number_format($u['nrr_tertimbang'], 3) }}</td>
                @endforeach
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="label">Jumlah NRR Tertimbang</td>
                <td colspan="{{ $jumlahUnsur }}">{{ number_format($hasil['total_indeks_skm'], 3) }}</td>
            </tr>
            <tr>
                <td class="label">Nilai IKM (x25)</td>
                <td colspan="{{ $jumlahUnsur }}">{{ number_format($hasil['nilai_akhir_skm'], 3) }} ({{ $hasil['mutu_akhir'] }})</td>
            </tr>
        </tfoot>
    </table>
@endif
