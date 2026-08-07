<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Services\ClusteringService;
use Illuminate\Http\Request;

class KlasterController extends Controller
{
    public function index(Request $request, ClusteringService $service)
    {
        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();

        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');

        $periode = $periodeId ? PeriodeSurvei::find($periodeId) : null;

        $jumlahKlaster = 4;
        $hasil = $periode
            ? $service->klasterPuskesmas($periode, $jumlahKlaster)
            : ['kelompok' => collect(), 'dikecualikan' => collect(), 'kodeUnsur' => []];

        return view('dinkes.klaster.index', [
            'periode' => $periode,
            'daftarPeriode' => $daftarPeriode,
            'kelompok' => $hasil['kelompok'],
            'dikecualikan' => $hasil['dikecualikan'],
            'kodeUnsur' => $hasil['kodeUnsur'],
        ]);
    }
}
