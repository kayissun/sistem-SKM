<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Services\SkmCalculatorService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(SkmCalculatorService $service)
    {
        $puskesmas = Auth::user()->puskesmas;
        $periodeAktif = PeriodeSurvei::where('is_active', true)->first();
        $jumlahPetugas = $puskesmas->users()->count();

        $hasilPeriodeAktif = $periodeAktif ? $service->hitung($puskesmas, $periodeAktif) : null;

        return view('puskesmas.dashboard', compact('puskesmas', 'periodeAktif', 'jumlahPetugas', 'hasilPeriodeAktif'));
    }
}
