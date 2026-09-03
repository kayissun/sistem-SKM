<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;
use App\Models\TindakLanjut;
use App\Models\UnsurPelayanan;
use App\Services\SkmCalculatorService;
use Illuminate\Http\Request;

class TindakLanjutController extends Controller
{
    /**
     * Monitoring Tindak Lanjut — Dinkes memantau progres TL seluruh faskes.
     */
    public function index(Request $request, SkmCalculatorService $service)
    {
        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();
        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');
        $periode = $periodeId ? PeriodeSurvei::find($periodeId) : null;

        $status = $request->input('status');
        $search = $request->input('search');

        $daftarPuskesmas = Puskesmas::where('is_active', true)
            ->whereIn('jenis', ['puskesmas', 'rsu'])
            ->orderBy('nama')
            ->get();

        // Query utama: semua TL dengan filter
        $queryTl = TindakLanjut::with(['puskesmas', 'unsurPelayanan', 'progress'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->whereHas('puskesmas', fn($sq) => $sq->where('nama', 'like', "%{$search}%")))
            ->orderByDesc('tahun')
            ->orderByDesc('triwulan')
            ->orderByDesc('created_at');

        $tindakLanjuts = $queryTl->paginate(20)->withQueryString();

        // Ringkasan per faskes (hanya untuk periode aktif)
        $dataFaskes = collect();
        $kodeUnsur = [];
        $namaUnsur = [];

        if ($periode) {
            $unsurAktif = UnsurPelayanan::aktif()->get(['kode', 'nama_unsur']);
            $kodeUnsur = $unsurAktif->pluck('kode')->all();
            $namaUnsur = $unsurAktif->pluck('nama_unsur', 'kode')->all();

            $periodeSebelumnya = PeriodeSurvei::where('tanggal_mulai', '<', $periode->tanggal_mulai)
                ->orderByDesc('tanggal_mulai')
                ->first();

            $faskesQuery = $daftarPuskesmas;
            if ($search) {
                $faskesQuery = $faskesQuery->filter(
                    fn($p) => str_contains(strtolower($p->nama), strtolower($search))
                );
            }

            foreach ($faskesQuery as $puskesmas) {
                $hasil = $service->hitung($puskesmas, $periode);
                if ($hasil['jumlah_responden'] === 0) continue;

                $hasilSebelumnya = null;
                if ($periodeSebelumnya) {
                    $hasilSebelumnya = $service->hitung($puskesmas, $periodeSebelumnya);
                }

                $queryTlFaskes = TindakLanjut::where('puskesmas_id', $puskesmas->id)
                    ->with(['unsurPelayanan', 'progress.createdBy']);
                if ($status) $queryTlFaskes->where('status', $status);

                $tindakLanjutsFaskes = $queryTlFaskes->orderByDesc('tahun')
                    ->orderByDesc('triwulan')
                    ->get();

                $totalTl = $tindakLanjutsFaskes->count();
                $totalProgress = $tindakLanjutsFaskes->sum(fn($tl) => $tl->progress->count());

                $dataFaskes->push([
                    'puskesmas'       => $puskesmas,
                    'hasil'           => $hasil,
                    'hasilSebelumnya' => $hasilSebelumnya,
                    'tindakLanjuts'   => $tindakLanjutsFaskes,
                    'totalTl'         => $totalTl,
                    'totalProgress'   => $totalProgress,
                ]);
            }
        }

        return view('dinkes.tindak-lanjut.index', compact(
            'daftarPeriode', 'daftarPuskesmas', 'periode',
            'dataFaskes', 'tindakLanjuts', 'kodeUnsur', 'namaUnsur',
            'status', 'search'
        ));
    }

    /**
     * Detail Tindak Lanjut — lihat detail + riwayat progres.
     */
    public function show(TindakLanjut $tindakLanjut)
    {
        $tindakLanjut->load(['puskesmas', 'unsurPelayanan', 'progress.createdBy', 'verifiedBy']);

        return view('dinkes.tindak-lanjut.show', compact('tindakLanjut'));
    }

    /**
     * Ringkasan per puskesmas — riwayat seluruh TL + progres.
     */
    public function rekapPuskesmas(Puskesmas $puskesma, Request $request)
    {
        $triwulan = $request->integer('triwulan');
        $tahun = $request->integer('tahun');

        $queryTl = TindakLanjut::where('puskesmas_id', $puskesma->id)
            ->with(['unsurPelayanan', 'progress'])
            ->when($triwulan, fn($q) => $q->where('triwulan', $triwulan))
            ->when($tahun, fn($q) => $q->where('tahun', $tahun))
            ->orderByDesc('tahun')
            ->orderByDesc('triwulan');

        $tindakLanjuts = $queryTl->get();

        return view('dinkes.tindak-lanjut.rekap-puskesmas', compact('puskesma', 'tindakLanjuts', 'triwulan', 'tahun'));
    }
}