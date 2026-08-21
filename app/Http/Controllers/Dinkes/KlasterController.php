<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Exports\KlasterExport;
use App\Models\ClusterResult;
use App\Models\PeriodeSurvei;
use App\Services\ClusteringService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class KlasterController extends Controller
{
    public function index(Request $request, ClusteringService $service)
    {
        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();

        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');

        $periode = $periodeId ? PeriodeSurvei::find($periodeId) : null;

        $jumlahKlaster = max(2, min(6, $request->integer('jumlah_klaster') ?: 4));
        $hasil = $periode
            ? $service->klasterPuskesmas($periode, $jumlahKlaster)
            : [
                'kelompok' => collect(),
                'insight' => collect(),
                'dikecualikan' => collect(),
                'kodeUnsur' => [],
                'namaUnsur' => [],
                'jumlahKlaster' => 0,
                'jumlahSampel' => 0,
                'peringatanKualitas' => null,
            ];

        $periodeSebelumnya = $periode
            ? $daftarPeriode
                ->filter(fn ($item) => $item->tanggal_mulai < $periode->tanggal_mulai)
                ->sortByDesc('tanggal_mulai')
                ->take(3)
                ->sortBy('tanggal_mulai')
                ->values()
            : collect();

        $periodeTren = $periodeSebelumnya->push($periode)->filter();
        $idsUnit = $hasil['kelompok']->flatMap(fn ($kelompok) => $kelompok['anggota']->pluck('id'))->all();
        $riwayat = ClusterResult::query()
            ->whereIn('puskesmas_id', $idsUnit)
            ->whereIn('periode', $periodeTren->pluck('id'))
            ->get()
            ->groupBy('puskesmas_id');

        $namaPeriode = $periodeTren->pluck('nama', 'id');
        $kelompok = $hasil['kelompok']->map(function ($kelompok) use ($riwayat, $namaPeriode) {
            $kelompok['anggota'] = $kelompok['anggota']->map(function ($anggota) use ($riwayat, $namaPeriode) {
                $anggota['tren'] = $riwayat->get($anggota['id'], collect())
                    ->sortBy('periode')
                    ->map(fn ($item) => [
                        'periode' => $namaPeriode[$item->periode] ?? $item->periode,
                        'cluster' => $item->cluster_nama ?: $item->cluster,
                        'nilai' => $item->nilai_rata2,
                    ])->values();

                return $anggota;
            });

            return $kelompok;
        });

        return view('dinkes.klaster.index', [
            'periode' => $periode,
            'daftarPeriode' => $daftarPeriode,
            'kelompok' => $kelompok,
            'insight' => $hasil['insight'],
            'dikecualikan' => $hasil['dikecualikan'],
            'kodeUnsur' => $hasil['kodeUnsur'],
            'namaUnsur' => $hasil['namaUnsur'],
            'jumlahKlaster' => $hasil['jumlahKlaster'],
            'jumlahSampel' => $hasil['jumlahSampel'],
            'peringatanKualitas' => $hasil['peringatanKualitas'],
        ]);
    }

    public function exportPdf(Request $request, ClusteringService $service)
    {
        [$periode, $hasil] = $this->hitung($request, $service);
        abort_if(! $periode, 404, 'Periode survei tidak ditemukan.');

        return Pdf::loadView('exports.klaster-pdf', [
            'periode' => $periode,
            'kelompok' => $hasil['kelompok'],
            'insight' => $hasil['insight'],
            'namaUnsur' => $hasil['namaUnsur'],
            'peringatanKualitas' => $hasil['peringatanKualitas'],
        ])->download("klaster-{$periode->id}.pdf");
    }

    public function exportExcel(Request $request, ClusteringService $service)
    {
        [$periode, $hasil] = $this->hitung($request, $service);
        abort_if(! $periode, 404, 'Periode survei tidak ditemukan.');

        return Excel::download(
            new KlasterExport($hasil['kelompok'], $hasil['insight'], $periode->nama),
            "klaster-{$periode->id}.xlsx"
        );
    }

    private function hitung(Request $request, ClusteringService $service): array
    {
        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');
        $periode = $periodeId ? PeriodeSurvei::find($periodeId) : null;
        $jumlahKlaster = max(2, min(6, $request->integer('jumlah_klaster') ?: 4));

        return [$periode, $periode ? $service->klasterPuskesmas($periode, $jumlahKlaster) : null];
    }
}
