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
        .ringkasan { margin-top: 20px; }
        .ringkasan td { border: none; padding: 2px 8px; }
        .ringkasan .label { color: #666; }
        .nilai-akhir { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Laporan Survei Kepuasan Masyarakat</h2>
    <p class="sub">{{ $puskesmas->nama }} &middot; Periode: {{ $periode->nama }} &middot; Dicetak: {{ now()->format('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Unsur Pelayanan</th>
                <th>Total Nilai</th>
                <th>NRR</th>
                <th>NRR Skala 100</th>
                <th>Kategori</th>
                <th>NRR Tertimbang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hasil['per_unsur'] as $kode => $unsur)
                <tr>
                    <td>{{ $kode }}</td>
                    <td>{{ $unsur['pertanyaan'] }}</td>
                    <td>{{ $unsur['total_nilai'] }}</td>
                    <td>{{ $unsur['nrr'] }}</td>
                    <td>{{ $unsur['nrr_skala_100'] }}</td>
                    <td>{{ $unsur['kategori'] }}</td>
                    <td>{{ $unsur['nrr_tertimbang'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="ringkasan">
        <tr>
            <td class="label">Jumlah responden</td>
            <td>{{ $hasil['jumlah_responden'] }}</td>
        </tr>
        <tr>
            <td class="label">Nilai akhir SKM</td>
            <td class="nilai-akhir">{{ $hasil['nilai_akhir_skm'] }}</td>
        </tr>
        <tr>
            <td class="label">Mutu akhir</td>
            <td>{{ $hasil['mutu_akhir'] }}</td>
        </tr>
    </table>
</body>
</html>
