<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Klaster Performa</title>
    <style>
        @page { margin: 28px 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 10px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 18px 0 6px; color: #43206f; }
        p { margin: 3px 0 8px; }
        .warning { padding: 8px; background: #fff4d6; border: 1px solid #e6c96a; margin: 10px 0; }
        .insight { padding: 7px 9px; background: #f3effa; border-left: 3px solid #6d28d9; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #d7d2df; padding: 6px; text-align: left; }
        th { background: #eee9f7; }
        .number { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Klaster Performa</h1>
    <p>Periode: <strong>{{ $periode->nama }}</strong></p>

    @if ($peringatanKualitas)
        <div class="warning">{{ $peringatanKualitas }}</div>
    @endif

    <h2>Insight Otomatis</h2>
    @foreach ($insight as $item)
        <div class="insight"><strong>{{ $item['cluster'] }}</strong>: {{ $item['kesimpulan'] }}</div>
    @endforeach

    <h2>Daftar Unit</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kelompok</th>
                <th>Unit</th>
                <th>Nilai SKM</th>
                <th>Mutu</th>
            </tr>
        </thead>
        <tbody>
            @php $nomor = 0; @endphp
            @foreach ($kelompok as $k)
                @foreach ($k['anggota'] as $anggota)
                    @php $nomor++; @endphp
                    <tr>
                        <td>{{ $nomor }}</td>
                        <td>{{ $k['label'] }}</td>
                        <td>{{ $anggota['nama'] }}</td>
                        <td class="number">{{ $anggota['nilai_akhir'] }}</td>
                        <td>{{ $anggota['mutu'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
