<?php

namespace App\Http\Controllers\Dinkes;

use App\Exports\LaporanUnsurExport;
use App\Exports\RekapGabunganExport;
use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\SurveiJawabanDetail;
use App\Services\SkmCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request, SkmCalculatorService $service)
    {
        [$periode, $daftarPeriode, $rekap] = $this->ambilRekapGabungan($request, $service);

        return view('dinkes.laporan.index', compact('rekap', 'periode', 'daftarPeriode'));
    }

    public function exportPdfGabungan(Request $request, SkmCalculatorService $service)
    {
        [$periode, , $rekap] = $this->ambilRekapGabungan($request, $service);

        abort_if(! $periode, 404, 'Periode survei tidak ditemukan.');

        $pdf = Pdf::loadView('exports.rekap-gabungan-pdf', compact('rekap', 'periode'));

        return $pdf->download("rekap-skm-{$periode->id}.pdf");
    }

    public function exportExcelGabungan(Request $request, SkmCalculatorService $service)
    {
        [$periode, , $rekap] = $this->ambilRekapGabungan($request, $service);

        abort_if(! $periode, 404, 'Periode survei tidak ditemukan.');

        return Excel::download(
            new RekapGabunganExport($rekap, $periode->nama),
            "rekap-skm-{$periode->id}.xlsx"
        );
    }

    public function detail(Request $request, Puskesmas $puskesma, SkmCalculatorService $service)
    {
        [$periode, $hasil] = $this->ambilHasilUnit($request, $puskesma, $service);

        return view('dinkes.laporan.detail', [
            'puskesmas' => $puskesma,
            'periode' => $periode,
            'hasil' => $hasil,
        ]);
    }

    public function exportPdfDetail(Request $request, Puskesmas $puskesma, SkmCalculatorService $service)
    {
        [$periode, $hasil] = $this->ambilHasilUnit($request, $puskesma, $service);

        $pdf = Pdf::loadView('exports.laporan-pdf', [
            'puskesmas' => $puskesma,
            'periode' => $periode,
            'hasil' => $hasil,
        ]);

        return $pdf->download("skm-{$puskesma->slug}-{$periode->id}.pdf");
    }

    public function exportExcelDetail(Request $request, Puskesmas $puskesma, SkmCalculatorService $service)
    {
        [, $hasil] = $this->ambilHasilUnit($request, $puskesma, $service);

        return Excel::download(
            new LaporanUnsurExport($hasil, 'SKM ' . $puskesma->nama),
            "skm-{$puskesma->slug}.xlsx"
        );
    }

    public function jawabanTeks(Request $request, Puskesmas $puskesma, PertanyaanSurvei $pertanyaan)
    {
        abort_unless($pertanyaan->puskesmas_id === $puskesma->id, 404);
        abort_unless($pertanyaan->tipe_input === 'teks', 404);

        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');

        $periode = PeriodeSurvei::findOrFail($periodeId);

        $daftarJawaban = SurveiJawabanDetail::where('pertanyaan_survei_id', $pertanyaan->id)
            ->whereNotNull('jawaban_teks')
            ->whereHas('surveiJawaban', function ($q) use ($puskesma, $periode) {
                $q->where('puskesmas_id', $puskesma->id)->where('periode_survei_id', $periode->id);
            })
            ->with('surveiJawaban')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dinkes.laporan.jawaban-teks', [
            'puskesmas' => $puskesma,
            'pertanyaan' => $pertanyaan,
            'periode' => $periode,
            'daftarJawaban' => $daftarJawaban,
        ]);
    }

    /**
     * @return array{0: ?PeriodeSurvei, 1: \Illuminate\Support\Collection<int, array>, 2: \Illuminate\Database\Eloquent\Collection}
     */
    private function ambilRekapGabungan(Request $request, SkmCalculatorService $service): array
    {
        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');

        $periode = $periodeId ? PeriodeSurvei::find($periodeId) : null;
        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();

        $rekap = $periode ? $service->hitungGabungan($periode) : collect();

        return [$periode, $daftarPeriode, $rekap];
    }

    private function ambilHasilUnit(Request $request, Puskesmas $puskesmas, SkmCalculatorService $service): array
    {
        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');

        $periode = PeriodeSurvei::findOrFail($periodeId);
        $hasil = $service->hitung($puskesmas, $periode);

        return [$periode, $hasil];
    }
}
