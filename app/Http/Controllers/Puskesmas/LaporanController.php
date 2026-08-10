<?php

namespace App\Http\Controllers\Puskesmas;

use App\Exports\LaporanUnsurExport;
use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\SurveiJawabanDetail;
use App\Services\SkmCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LaporanController extends Controller
{
    public function index(Request $request, SkmCalculatorService $service)
    {
        [$puskesmas, $periode, $daftarPeriode, $hasil] = $this->ambilData($request, $service);

        $hasilPerPoli = ($puskesmas && $periode) ? $service->hitungPerUnitLayanan($puskesmas, $periode) : collect();

        $kodeUnsur = [];
        $daftarResponden = collect();
        $respondenRows = collect();

        if ($puskesmas && $periode) {
            $kodeUnsur = \App\Models\UnsurPelayanan::aktif()->pluck('kode')->all();

            $daftarResponden = \App\Models\SurveiJawabanDetail::query()
                ->whereHas('surveiJawaban', function ($q) use ($puskesmas, $periode) {
                    $q->where('puskesmas_id', $puskesmas->id)->where('periode_survei_id', $periode->id);
                })
                ->with('surveiJawaban.detail.pertanyaanSurvei.unsurPelayanan')
                ->get()
                ->groupBy('survei_jawaban_id')
                ->map(fn($details) => $details->first()->surveiJawaban)
                ->values();

            // simple paginate
            $perPage = 100;
            $currentPage = max(1, (int) ($request->query('page', 1)));
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $daftarResponden->forPage($currentPage, $perPage),
                $daftarResponden->count(),
                $perPage,
                $currentPage,
                ['path' => url()->current(), 'query' => $request->query()]
            );

            foreach ($paginated as $i => $jawaban) {
                $row = ['no' => ($i + 1) + (($currentPage - 1) * $perPage)];
                // Demographic / respondent fields
                $row['unit'] = $jawaban->unitLayanan ? $jawaban->unitLayanan->unit_layanan_nama ?? $jawaban->unitLayanan->nama ?? '-' : '-';
                $row['usia_rentang'] = $jawaban->usia_rentang;
                $row['jenis_kelamin'] = $jawaban->jenis_kelamin;
                $row['pendidikan'] = $jawaban->pendidikan;
                $row['pekerjaan'] = $jawaban->pekerjaan;

                $total = 0;
                foreach ($kodeUnsur as $kode) {
                    $values = $jawaban->detail->filter(function ($d) use ($kode) {
                        return $d->pertanyaanSurvei && $d->pertanyaanSurvei->unsurPelayanan && $d->pertanyaanSurvei->unsurPelayanan->kode === $kode;
                    })->pluck('nilai')->filter()->all();

                    if (count($values) > 0) {
                        $avg = (int) round(array_sum($values) / count($values));
                        $row[$kode] = $avg;
                        $total += $avg;
                    } else {
                        $row[$kode] = null;
                    }
                }
                $row['total'] = $total;
                $row['nama'] = $jawaban->nama;
                $respondenRows->push($row);
            }

            $daftarResponden = $paginated;
        }

        return view('puskesmas.laporan.index', compact('puskesmas', 'daftarPeriode', 'periode', 'hasil', 'hasilPerPoli'))
            ->with([ 'kodeUnsur' => $kodeUnsur, 'daftarResponden' => $daftarResponden, 'respondenRows' => $respondenRows ]);
    }

    public function exportPdf(Request $request, SkmCalculatorService $service)
    {
        [$puskesmas, $periode, , $hasil] = $this->ambilData($request, $service);

        abort_if(! $periode || ! $hasil, 404, 'Periode survei tidak ditemukan.');

        $hasilPerPoli = $service->hitungPerUnitLayanan($puskesmas, $periode);

        $pdf = Pdf::loadView('exports.laporan-pdf', compact('puskesmas', 'periode', 'hasil', 'hasilPerPoli'));

        return $pdf->download("skm-{$puskesmas->slug}-{$periode->id}.pdf");
    }

    public function exportExcel(Request $request, SkmCalculatorService $service)
    {
        [$puskesmas, $periode, , $hasil] = $this->ambilData($request, $service);

        abort_if(! $hasil, 404, 'Periode survei tidak ditemukan.');

        $hasilPerPoli = $service->hitungPerUnitLayanan($puskesmas, $periode);

        return Excel::download(
            new LaporanUnsurExport($hasil, $hasilPerPoli),
            "skm-{$puskesmas->slug}.xlsx"
        );
    }

    public function jawabanTeks(Request $request, PertanyaanSurvei $pertanyaan)
    {
        if ($pertanyaan->puskesmas_id !== Auth::user()->puskesmas_id) {
            throw new AccessDeniedHttpException('Pertanyaan ini bukan milik unit Anda.');
        }

        abort_unless($pertanyaan->tipe_input === 'teks', 404);

        $puskesmas = Auth::user()->puskesmas;

        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');

        $periode = PeriodeSurvei::findOrFail($periodeId);

        $daftarJawaban = SurveiJawabanDetail::where('pertanyaan_survei_id', $pertanyaan->id)
            ->whereNotNull('jawaban_teks')
            ->whereHas('surveiJawaban', function ($q) use ($puskesmas, $periode) {
                $q->where('puskesmas_id', $puskesmas->id)->where('periode_survei_id', $periode->id);
            })
            ->with('surveiJawaban')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('puskesmas.laporan.jawaban-teks', compact('puskesmas', 'pertanyaan', 'periode', 'daftarJawaban'));
    }

    private function ambilData(Request $request, SkmCalculatorService $service): array
    {
        $puskesmas = Auth::user()->puskesmas;

        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();

        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');

        $periode = $periodeId ? PeriodeSurvei::find($periodeId) : null;

        $hasil = ($puskesmas && $periode) ? $service->hitung($puskesmas, $periode) : null;

        return [$puskesmas, $periode, $daftarPeriode, $hasil];
    }
}
