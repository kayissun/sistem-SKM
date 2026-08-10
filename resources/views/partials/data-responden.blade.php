<div>
    <h5 class="mb-2">Data Responden</h5>
    @if (empty($daftarResponden) || $daftarResponden->count() === 0)
        <p class="text-muted">Belum ada responden untuk periode ini.</p>
    @else
        <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered bg-white" style="font-size:0.85rem">
                <thead>
                    <tr>
                        <th style="width:60px">No.</th>
                        <th>Jenis Unit</th>
                        <th style="width:80px">Umur</th>
                        <th style="width:80px">Jenis Kelamin</th>
                        <th style="width:160px">Pendidikan Terakhir</th>
                        <th style="width:160px">Pekerjaan Utama</th>
                        @foreach ($kodeUnsur as $kode)
                            <th class="text-center">{{ $kode }}</th>
                        @endforeach
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($respondenRows as $row)
                        <tr>
                            <td>{{ $row['no'] }}</td>
                            <td>{{ $row['unit'] ?? '-' }}</td>
                            <td>{{ $row['usia_rentang'] ?? '-' }}</td>
                            <td>{{ $row['jenis_kelamin'] ?? '-' }}</td>
                            <td>{{ $row['pendidikan'] ?? '-' }}</td>
                            <td>{{ $row['pekerjaan'] ?? '-' }}</td>
                            @foreach ($kodeUnsur as $kode)
                                <td class="text-center">{{ $row[$kode] ?? '-' }}</td>
                            @endforeach
                            <td class="text-center">{{ $row['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (isset($daftarResponden) && method_exists($daftarResponden, 'links'))
            <div class="d-flex justify-content-end">{{ $daftarResponden->links() }}</div>
        @endif
    @endif
</div>
