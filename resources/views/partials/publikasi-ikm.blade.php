@php
    $labelPendidikan = fn ($kode) => \App\Support\OpsiDataDiri::labelPendidikan($kode);
@endphp

<div class="border rounded p-4 bg-white mx-auto" style="max-width:700px">
    <div class="text-center mb-3">
        <h5 class="fw-bold mb-1">INDEKS KEPUASAN MASYARAKAT (IKM)</h5>
        <div class="fw-bold text-uppercase">{{ $namaOrganisasi }}</div>
        <div class="fw-bold text-uppercase">{{ $namaUnit }}</div>
        <div class="fw-bold text-uppercase">{{ $periode->nama }}</div>
    </div>

    <div class="row g-0 border">
        <div class="col-5 border-end d-flex flex-column">
            <div class="text-center fw-bold border-bottom py-2">NILAI IKM</div>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center py-4">
                <span style="font-size:3.2rem; font-weight:800; line-height:1">
                    {{ number_format($publikasi['nilai_akhir_skm'], 2, ',', '.') }}
                </span>
            </div>
            <div class="text-center text-muted small pb-2">{{ $publikasi['mutu_akhir'] }}</div>
        </div>

        <div class="col-7">
            <div class="px-3 py-2 border-bottom">
                <strong>NAMA LAYANAN</strong> : {{ $namaLayanan }}
            </div>

            <table class="table table-bordered table-sm mb-0" style="font-size:0.85rem">
                <thead>
                    <tr><th colspan="2" class="text-center">RESPONDEN</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width:55%">JUMLAH</td>
                        <td>: {{ $publikasi['jumlah_responden'] }}</td>
                    </tr>
                    <tr>
                        <td>JENIS KELAMIN</td>
                        <td>: L : {{ $publikasi['jumlah_laki'] }} &nbsp; P : {{ $publikasi['jumlah_perempuan'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">PENDIDIKAN</td>
                    </tr>
                    @foreach ($publikasi['pendidikan'] as $kode => $jumlah)
                        <tr>
                            <td>{{ $labelPendidikan($kode) }}</td>
                            <td>: {{ $jumlah }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4 small">
        <p class="mb-1">TERIMA KASIH ATAS PENILAIAN YANG TELAH ANDA BERIKAN</p>
        <p class="mb-0">
            MASUKAN ANDA SANGAT BERMANFAAT UNTUK KEMAJUAN UNIT KAMI AGAR TERUS MEMPERBAIKI
            DAN MENINGKATKAN KUALITAS PELAYANAN BAGI MASYARAKAT
        </p>
    </div>
</div>
