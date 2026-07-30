<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class AktivitasController extends Controller
{
    public function index()
    {
        $daftarAktivitas = Activity::with('causer', 'subject')
            ->latest()
            ->paginate(25);

        return view('dinkes.aktivitas.index', compact('daftarAktivitas'));
    }
}
