<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan SKM {{ $puskesmas->nama }}</title>
    <style>
        @page { margin: 12mm 12mm 16mm; }

        body { font-family: sans-serif; font-size: 11px; color: #222; }
        h2 { margin-bottom: 0; }
        h3 { margin-top: 22px; margin-bottom: 6px; font-size: 13px; }
        p.sub { color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; text-align: center; }
        th { background: #f7f0da; }
        td.label { text-align: left; }
        th.label { text-align: left; }
        tfoot td { background: #f2f2f2; font-weight: bold; }
        .ringkasan { margin-top: 14px; width: auto; }
        .ringkasan td { border: none; padding: 2px 8px; text-align: left; }
        .ringkasan .label { color: #666; }
        .nilai-akhir { font-size: 16px; font-weight: bold; }
        .poli-block { margin-top: 18px; }
        th, td { vertical-align: top; }

        /* Footer nomor halaman — dirender DomPDF di tiap halaman */
        .page-footer {
            position: fixed;
            bottom: -9mm;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 8px;
            color: #888;
        }
    </style>
</head>
<body>
    <h2>Laporan Survei Kepuasan Masyarakat</h2>
    <p class="sub">{{ $puskesmas->nama }} &middot; Periode: {{ $periode->nama }} &middot; Dicetak: {{ now()->format('d M Y H:i') }}</p>

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

    <h3>Indeks Kepuasan Masyarakat — Seluruh Layanan</h3>
    @include('exports.partials.matriks-skm-pdf', ['hasil' => $hasil])

    @isset($hasilPerPoli)
        @foreach ($hasilPerPoli as $poli)
            <div class="poli-block">
                <h3>IKM Poli/Layanan: {{ $poli['unit_layanan_nama'] }}</h3>
                @include('exports.partials.matriks-skm-pdf', ['hasil' => $poli])
            </div>
        @endforeach
    @endisset

    @if (!empty($hasil['pertanyaan_tambahan']))
        <h3>Pertanyaan Tambahan (di luar nilai SKM resmi)</h3>
        <table>
            <thead>
                <tr>
                    <th class="label">Pertanyaan</th>
                    <th>Jumlah Jawaban</th>
                    <th>Rata-rata</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasil['pertanyaan_tambahan'] as $tambahan)
                    <tr>
                        <td class="label">{{ $tambahan['teks_pertanyaan'] }}</td>
                        <td>{{ $tambahan['jumlah_jawaban'] }}</td>
                        <td>{{ $tambahan['rata_rata'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="page-footer">
        Halaman {PAGE_NUM} dari {PAGE_COUNT}
    </div>
</body>
</html>
