<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Puskesmas\PertanyaanSurveiRequest;
use App\Models\PertanyaanSurvei;
use App\Models\UnsurPelayanan;
use App\Support\PresetLabelSkala;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PertanyaanSurveiController extends Controller
{
    public function index()
    {
        $puskesmasId = Auth::user()->puskesmas_id;

        $daftarPertanyaan = PertanyaanSurvei::with('unsurPelayanan')
            ->where('puskesmas_id', $puskesmasId)
            ->orderBy('urutan')
            ->get();

        // unsur wajib (U1-U9) yang belum/tidak punya pertanyaan aktif di unit ini,
        // supaya admin sadar kalau ada unsur yang bolong dan nilai SKM-nya jadi tidak akurat
        $unsurTerpakai = $daftarPertanyaan->where('is_active', true)->pluck('unsur_pelayanan_id')->filter();
        $unsurBelumAda = UnsurPelayanan::aktif()->get()->reject(fn ($unsur) => $unsurTerpakai->contains($unsur->id));

        return view('puskesmas.pertanyaan.index', compact('daftarPertanyaan', 'unsurBelumAda'));
    }

    public function create()
    {
        $daftarUnsur = UnsurPelayanan::aktif()->get();
        $presetLabel = PresetLabelSkala::daftar();
        $urutanBerikutnya = (PertanyaanSurvei::where('puskesmas_id', Auth::user()->puskesmas_id)->max('urutan') ?? 0) + 1;

        return view('puskesmas.pertanyaan.create', compact('daftarUnsur', 'presetLabel', 'urutanBerikutnya'));
    }

    public function store(PertanyaanSurveiRequest $request)
    {
        PertanyaanSurvei::create([
            'puskesmas_id' => Auth::user()->puskesmas_id,
            ...$request->validated(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('puskesmas.pertanyaan.index')
            ->with('success', 'Pertanyaan survei berhasil ditambahkan.');
    }

    public function edit(PertanyaanSurvei $pertanyaan)
    {
        $this->pastikanSatuUnit($pertanyaan);

        $daftarUnsur = UnsurPelayanan::aktif()->get();
        $presetLabel = PresetLabelSkala::daftar();

        return view('puskesmas.pertanyaan.edit', compact('pertanyaan', 'daftarUnsur', 'presetLabel'));
    }

    public function update(PertanyaanSurveiRequest $request, PertanyaanSurvei $pertanyaan)
    {
        // kepemilikan unit sudah divalidasi di PertanyaanSurveiRequest::authorize()
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $pertanyaan->update($data);

        return redirect()
            ->route('puskesmas.pertanyaan.index')
            ->with('success', 'Pertanyaan survei berhasil diperbarui.');
    }

    public function destroy(PertanyaanSurvei $pertanyaan)
    {
        $this->pastikanSatuUnit($pertanyaan);

        if ($pertanyaan->surveiJawabanDetail()->exists()) {
            return redirect()
                ->route('puskesmas.pertanyaan.index')
                ->with('error', 'Pertanyaan ini sudah punya jawaban responden, nonaktifkan saja daripada dihapus.');
        }

        $pertanyaan->delete();

        return redirect()
            ->route('puskesmas.pertanyaan.index')
            ->with('success', 'Pertanyaan survei berhasil dihapus.');
    }

    /**
     * Dipakai untuk edit() (tampilan form) dan destroy() (tidak ada FormRequest karena
     * tidak ada input body). Untuk store()/update(), pengecekan yang sama sudah dilakukan
     * di PertanyaanSurveiRequest::authorize().
     */
    private function pastikanSatuUnit(PertanyaanSurvei $pertanyaan): void
    {
        if ($pertanyaan->puskesmas_id !== Auth::user()->puskesmas_id) {
            throw new AccessDeniedHttpException('Pertanyaan ini bukan milik unit Anda.');
        }
    }
}
