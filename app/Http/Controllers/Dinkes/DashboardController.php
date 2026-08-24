<?php

namespace App\Http\Controllers\Dinkes;

use App\Models\ClusterResult;
use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;
use App\Services\ClusteringService;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahUnit = Puskesmas::where('is_active', true)->count();
        $periodeAktif = PeriodeSurvei::where('is_active', true)->first();

        $clusters = ClusterResult::with('puskesmas')
            ->when($periodeAktif, fn ($query) => $query->where('periode', $periodeAktif->id))
            ->latest()
            ->get();

        return view('dinkes.dashboard', compact('jumlahUnit', 'periodeAktif', 'clusters'));
    }

    public function generateCluster(ClusteringService $service)
    {
        $periode = PeriodeSurvei::where('is_active', true)->first();

        if (!$periode) {
            return back()->with('error', 'Tidak ada periode survei aktif.');
        }

        // Satu-satunya jalur yang MENYIMPAN hasil klaster ke cluster_results
        // (dipakai sebagai riwayat tren di halaman Klaster Performa).
        $service->klasterPuskesmas($periode, 4, simpanKeDb: true);

        return back()->with('success', 'Clustering berhasil dijalankan dan disimpan sebagai riwayat tren.');
    }
}
