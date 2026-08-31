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
        $user = Auth::user();
        $puskesmas = $user->puskesmas;
        $periodeAktif = PeriodeSurvei::where('is_active', true)->first();

        if ($user->hasRole('dinkes-skm')) {
            $hasilPeriodeAktif = $puskesmas && $periodeAktif ? $service->hitung($puskesmas, $periodeAktif) : null;

            return view('puskesmas.dashboard', compact('puskesmas', 'periodeAktif', 'hasilPeriodeAktif'))
                ->with('roleLabel', 'Dinkes SKM');
        }

        $hasilPeriodeAktif = $periodeAktif ? $service->hitung($puskesmas, $periodeAktif) : null;

        return view('puskesmas.dashboard', compact('puskesmas', 'periodeAktif', 'hasilPeriodeAktif'))
            ->with('roleLabel', 'Admin Puskesmas');
    }
}
