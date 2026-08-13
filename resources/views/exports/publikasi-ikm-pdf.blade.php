<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; color: #222; }
        .judul { text-align: center; }
        .judul h2 { margin: 0 0 4px; font-size: 15px; }
        .judul div { font-weight: bold; text-transform: uppercase; font-size: 12px; margin-bottom: 2px; }
        .kotak { border: 1px solid #333; width: 100%; border-collapse: collapse; margin-top: 14px; }
        .kotak td { border: 1px solid #333; vertical-align: top; padding: 0; }
        .kolom-nilai { width: 40%; text-align: center; }
        .label-nilai { font-weight: bold; padding: 8px; border-bottom: 1px solid #333; }
        .angka-nilai { font-size: 46px; font-weight: 800; padding: 30px 0; }
        .mutu-nilai { padding: 8px; color: #555; font-size: 11px; }
        .layanan-header { padding: 8px; border-bottom: 1px solid #333; font-weight: bold; font-size: 12px; }
        table.responden { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.responden th, table.responden td { border: 1px solid #999; padding: 4px 6px; }
        table.responden th { background: #f2f2f2; text-align: center; }
        .footer-text { text-align: center; margin-top: 20px; font-size: 11px; }
        .footer-text p { margin: 2px 0; }
    </style>
</head>
<body>
    <div class="judul">
        <h2>INDEKS KEPUASAN MASYARAKAT (IKM)</h2>
        <div>{{ config('organisasi.nama') }}</div>
        <div>{{ $puskesmas->nama }}</div>
        <div>{{ $periode->nama }}</div>
    </div>

    <table class="kotak">
        <tr>
            <td class="kolom-nilai">
                <div class="label-nilai">NILAI IKM</div>
                <div class="angka-nilai">{{ number_format($publikasi['nilai_akhir_skm'], 2, ',', '.') }}</div>
                <div class="mutu-nilai">{{ $publikasi['mutu_akhir'] }}</div>
            </td>
            <td>
                <div class="layanan-header">NAMA LAYANAN : {{ $namaLayanan }}</div>
                <table class="responden">
                    <tr><th colspan="2">RESPONDEN</th></tr>
                    <tr><td style="width:55%">JUMLAH</td><td>: {{ $publikasi['jumlah_responden'] }}</td></tr>
                    <tr><td>JENIS KELAMIN</td><td>: L : {{ $publikasi['jumlah_laki'] }} &nbsp; P : {{ $publikasi['jumlah_perempuan'] }}</td></tr>
                    <tr><td colspan="2">PENDIDIKAN</td></tr>
                    @foreach ($publikasi['pendidikan'] as $kode => $jumlah)
                        <tr>
                            <td>{{ \App\Support\OpsiDataDiri::labelPendidikan($kode) }}</td>
                            <td>: {{ $jumlah }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <div class="footer-text">
        <p>TERIMA KASIH ATAS PENILAIAN YANG TELAH ANDA BERIKAN</p>
        <p>MASUKAN ANDA SANGAT BERMANFAAT UNTUK KEMAJUAN UNIT KAMI AGAR TERUS MEMPERBAIKI</p>
        <p>DAN MENINGKATKAN KUALITAS PELAYANAN BAGI MASYARAKAT</p>
    </div>
</body>
</html>
