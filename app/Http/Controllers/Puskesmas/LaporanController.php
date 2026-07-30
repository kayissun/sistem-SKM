<?php

namespace App\Http\Controllers\Puskesmas;

use App\Exports\LaporanUnsurExport;
use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Services\SkmCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request, SkmCalculatorService $service)
    {
        [$puskesmas, $periode, $daftarPeriode, $hasil] = $this->ambilData($request, $service);

        return view('puskesmas.laporan.index', compact('puskesmas', 'daftarPeriode', 'periode', 'hasil'));
    }

    public function exportPdf(Request $request, SkmCalculatorService $service)
    {
        [$puskesmas, $periode, , $hasil] = $this->ambilData($request, $service);

        abort_if(! $periode || ! $hasil, 404, 'Periode survei tidak ditemukan.');

        $pdf = Pdf::loadView('exports.laporan-pdf', compact('puskesmas', 'periode', 'hasil'));

        return $pdf->download("skm-{$puskesmas->slug}-{$periode->id}.pdf");
    }

    public function exportExcel(Request $request, SkmCalculatorService $service)
    {
        [$puskesmas, , , $hasil] = $this->ambilData($request, $service);

        abort_if(! $hasil, 404, 'Periode survei tidak ditemukan.');

        return Excel::download(
            new LaporanUnsurExport($hasil, 'SKM ' . $puskesmas->nama),
            "skm-{$puskesmas->slug}.xlsx"
        );
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
