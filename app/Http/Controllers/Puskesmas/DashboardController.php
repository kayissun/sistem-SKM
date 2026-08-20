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
            $jumlahPetugas = $puskesmas ? $puskesmas->users()->count() : 0;
            $hasilPeriodeAktif = $puskesmas && $periodeAktif ? $service->hitung($puskesmas, $periodeAktif) : null;

            return view('puskesmas.dashboard', compact('puskesmas', 'periodeAktif', 'jumlahPetugas', 'hasilPeriodeAktif'))
                ->with('roleLabel', 'Dinkes SKM');
        }

        $jumlahPetugas = $puskesmas->users()->count();
        $hasilPeriodeAktif = $periodeAktif ? $service->hitung($puskesmas, $periodeAktif) : null;

        return view('puskesmas.dashboard', compact('puskesmas', 'periodeAktif', 'jumlahPetugas', 'hasilPeriodeAktif'))
            ->with('roleLabel', 'Admin Puskesmas');
    }
}
