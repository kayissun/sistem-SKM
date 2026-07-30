<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahUnit = Puskesmas::where('is_active', true)->count();
        $periodeAktif = PeriodeSurvei::where('is_active', true)->first();

        return view('dinkes.dashboard', compact('jumlahUnit', 'periodeAktif'));
    }
}
