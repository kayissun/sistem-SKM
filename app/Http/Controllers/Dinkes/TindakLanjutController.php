<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TindakLanjutController extends Controller
{
    /**
     * Daftar semua Tindak Lanjut dari seluruh puskesmas (view dinkes).
     */
    public function index(Request $request)
    {
        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();

        $triwulan = $request->integer('triwulan');
        $tahun = $request->integer('tahun');
        $status = $request->input('status');
        $puskesmasId = $request->integer('puskesmas_id');

        $query = TindakLanjut::with(['puskesmas', 'unsurPelayanan', 'progress']);

        if ($triwulan) {
            $query->where('triwulan', $triwulan);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($puskesmasId) {
            $query->where('puskesmas_id', $puskesmasId);
        }

        $tindakLanjuts = $query->orderByDesc('tahun')
            ->orderByDesc('triwulan')
            ->paginate(20)
            ->withQueryString();

        $daftarPuskesmas = Puskesmas::where('is_active', true)
            ->whereIn('jenis', ['puskesmas', 'rsu'])
            ->orderBy('nama')
            ->get();

        return view('dinkes.tindak-lanjut.index', compact(
            'tindakLanjuts', 'daftarPeriode', 'daftarPuskesmas',
            'triwulan', 'tahun', 'status', 'puskesmasId'
        ));
    }

    /**
     * Detail Tindak Lanjut tertentu.
     */
    public function show(TindakLanjut $tindakLanjut)
    {
        $tindakLanjut->load(['puskesmas', 'unsurPelayanan', 'progress', 'verifiedBy']);

        return view('dinkes.tindak-lanjut.show', compact('tindakLanjut'));
    }

    /**
     * Setujui Tindak Lanjut.
     */
    public function approve(TindakLanjut $tindakLanjut, Request $request)
    {
        $validated = $request->validate([
            'catatan_dinkes' => 'nullable|string|max:1000',
        ]);

        $tindakLanjut->update([
            'status' => TindakLanjut::STATUS_APPROVED,
            'catatan_dinkes' => $validated['catatan_dinkes'] ?? null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('dinkes.tindak-lanjut.show', $tindakLanjut)
            ->with('success', 'Tindak lanjut berhasil disetujui.');
    }

    /**
     * Tolak Tindak Lanjut.
     */
    public function reject(TindakLanjut $tindakLanjut, Request $request)
    {
        $validated = $request->validate([
            'catatan_dinkes' => 'required|string|min:5|max:1000',
        ]);

        $tindakLanjut->update([
            'status' => TindakLanjut::STATUS_REJECTED,
            'catatan_dinkes' => $validated['catatan_dinkes'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('dinkes.tindak-lanjut.show', $tindakLanjut)
            ->with('success', 'Tindak lanjut ditolak dan dikembalikan ke admin puskesmas.');
    }

    /**
     * Ringkasan per puskesmas — lihat capaian triwulan per unsur.
     */
    public function rekapPuskesmas(Puskesmas $puskesma)
    {
        $tindakLanjuts = TindakLanjut::where('puskesmas_id', $puskesma->id)
            ->with(['unsurPelayanan', 'progress'])
            ->orderByDesc('tahun')
            ->orderByDesc('triwulan')
            ->get();

        return view('dinkes.tindak-lanjut.rekap-puskesmas', compact('puskesma', 'tindakLanjuts'));
    }
}
