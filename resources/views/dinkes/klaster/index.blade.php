@extends('layouts.dinkes')

@section('title', 'Klaster Performa Unit')

@section('content')
    <h3 class="mb-3">Klaster Performa Unit (K-Means)</h3>

    <form method="GET" class="row g-2 mb-4" style="max-width:600px">
        <div class="col-md-6">
            <label class="form-label small fw-bold">Periode Survei</label>
            <select name="periode_survei_id" class="form-select" onchange="this.form.submit()">
                @foreach ($daftarPeriode as $p)
                    <option value="{{ $p->id }}" @selected($periode && $periode->id === $p->id)>
                        {{ $p->nama }} @if($p->is_active) (aktif) @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Jumlah Klaster (K)</label>
            <select name="jumlah_klaster" class="form-select" onchange="this.form.submit()">
                <option value="2" @selected($jumlahKlaster === 2)>2 Klaster</option>
                <option value="3" @selected($jumlahKlaster === 3)>3 Klaster</option>
                <option value="4" @selected($jumlahKlaster === 4)>4 Klaster</option>
            </select>
        </div>
    </form>

    @if (!$periode)
        <div class="alert alert-warning">Belum ada periode survei. Buat periode terlebih dahulu.</div>
    @elseif ($kelompok->isEmpty())
        <div class="alert alert-warning">
            Belum cukup data untuk dikelompokkan pada periode ini — minimal butuh 1 unit dengan
            responden dan semua 9 unsur wajib sudah dipetakan ke pertanyaan.
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="table table-bordered bg-white" style="min-width: 900px;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 30px;">Klaster</th>
                        <th>Unit Puskesmas/RSU</th>
                        <th class="text-center" style="width: 100px;">Nilai SKM</th>
                        <th class="text-center" style="width: 120px;">Mutu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kelompok as $kelompokIdx => $k)
                        @php
                            $isFirstInGroup = true;
                        @endphp
                        @foreach ($k['anggota'] as $anggota)
                            <tr>
                                @if ($isFirstInGroup)
                                    <td rowspan="{{ $k['anggota']->count() }}" class="text-center align-middle bg-light fw-bold">
                                        <div>{{ $k['label'] }}</div>
                                        <small class="text-muted d-block mt-1">Rata-rata:<br>{{ $k['rata_rata_skor'] }}</small>
                                    </td>
                                    @php $isFirstInGroup = false; @endphp
                                @endif
                                <td>{{ $anggota['nama'] }}</td>
                                <td class="text-center">{{ $anggota['nilai_akhir_skm'] }}</td>
                                <td class="text-center">{{ $anggota['mutu_akhir'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($dikecualikan->isNotEmpty())
            <div class="alert alert-secondary mt-3">
                <strong>{{ $dikecualikan->count() }} Unit tidak dikelompokkan:</strong>
                <ul class="mb-0 mt-2 small">
                    @foreach ($dikecualikan as $item)
                        <li><strong>{{ $item['nama'] }}</strong> — {{ $item['alasan'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
@endsection
