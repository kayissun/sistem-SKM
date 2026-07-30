<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h2 { margin-bottom: 0; }
        p.sub { color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Rekap Survei Kepuasan Masyarakat - Semua Unit</h2>
    <p class="sub">Periode: {{ $periode->nama }} &middot; Dicetak: {{ now()->format('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Unit</th>
                <th>Jumlah Responden</th>
                <th>Nilai Akhir SKM</th>
                <th>Mutu</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekap as $baris)
                <tr>
                    <td>{{ $baris['puskesmas'] }}</td>
                    <td>{{ $baris['jumlah_responden'] }}</td>
                    <td>{{ $baris['nilai_akhir_skm'] }}</td>
                    <td>{{ $baris['mutu_akhir'] }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
