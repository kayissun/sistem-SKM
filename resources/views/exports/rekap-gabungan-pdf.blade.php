<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Atur orientasi kertas ke Lanskap / Horizontal */
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body { 
            font-family: sans-serif; 
            font-size: 9px; /* Ukuran font disesuaikan sedikit agar lebih rapi */
            color: #222; 
            margin: 0;
        }
        h2 { margin-bottom: 0; }
        p.sub { color: #666; margin-top: 4px; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 12px; 
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
        }
        th { background: #f7f0da; }
        td.label { text-align: left; }
        th.label { text-align: left; }
    </style>
</head>
<body>
    <h2>Rekap Survei Kepuasan Masyarakat - Semua Unit</h2>
    <p class="sub">Periode: {{ $periode->nama }} &middot; Dicetak: {{ now()->format('d M Y H:i') }}</p>

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
                    <td>{{ $periode->nama }}</td>
                    @foreach ($kodeUnsur as $kode)
                        <td>{{ number_format($baris['per_unsur'][$kode]['nrr_skala_100'] ?? 0, 2) }}</td>
                    @endforeach
                    <td>{{ $baris['nilai_akhir_skm'] }}</td>
                    <td>{{ $baris['mutu_akhir'] }}</td>
                    <td>{{ $baris['jumlah_responden'] }}</td>
                    <td>SKM Online</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            @empty
                <tr><td colspan="{{ 9 + count($kodeUnsur) }}">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>