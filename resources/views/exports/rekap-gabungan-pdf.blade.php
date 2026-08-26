<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap SKM Semua Unit</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 10mm 14mm;
        }

        body {
            font-family: sans-serif;
            font-size: 9px;
            color: #222;
            margin: 0;
        }

        h2 { margin: 0 0 2px; font-size: 14px; }
        p.sub { color: #666; margin: 0 0 4px; font-size: 9px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 4px 5px;
            text-align: center;
            vertical-align: top;
        }

        th { background: #f7f0da; }
        td.label, th.label { text-align: left; }

        /* Footer nomor halaman — dirender DomPDF di tiap halaman */
        .page-footer {
            position: fixed;
            bottom: -8mm;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 8px;
            color: #888;
        }
    </style>
</head>
<body>
    <h2>Rekap Survei Kepuasan Masyarakat &mdash; Semua Unit</h2>
    <p class="sub">Periode: {{ $periode->nama }} &middot; Dicetak: {{ now()->translatedFormat('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2" class="label">OPD/Unit Pelayanan Publik</th>
                <th rowspan="2">Periode Pelaksanaan</th>
                <th colspan="{{ count($kodeUnsur) }}">Nilai Per Unsur</th>
                <th rowspan="2">IKM</th>
                <th rowspan="2">Kategori</th>
                <th rowspan="2">Jumlah Responden</th>
                <th rowspan="2">Metode SKM</th>
                <th rowspan="2">Unsur Prioritas Perbaikan</th>
                <th rowspan="2">Rencana Tindak Lanjut</th>
            </tr>
            <tr>
                @foreach ($kodeUnsur as $kode)
                    <th>{{ $kode }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rekap as $i => $baris)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="label">{{ $baris['puskesmas'] }}</td>
                    <td>{{ $namaPeriodeLengkap ?? $periode?->nama }}</td>
                    @foreach ($kodeUnsur as $kode)
                        <td>{{ number_format($baris['per_unsur'][$kode]['nrr_skala_100'] ?? 0, 2) }}</td>
                    @endforeach
                    <td>{{ number_format($baris['nilai_akhir_skm'], 2) }}</td>
                    <td>{{ $baris['mutu_akhir'] }}</td>
                    <td>{{ $baris['jumlah_responden'] }}</td>
                    <td>SKM Online</td>
                    <td class="label">
                        @foreach ($baris['unsur_prioritas'] as $prioritas)
                            <div>{{ $prioritas }}</div>
                        @endforeach
                    </td>
                    <td class="label">
                        @foreach ($baris['rencana_tindak_lanjut'] as $rencana)
                            <div>{{ $rencana }}</div>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ 10 + count($kodeUnsur) }}">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-footer">
        Halaman {PAGE_NUM} dari {PAGE_COUNT}
    </div>
</body>
</html>
