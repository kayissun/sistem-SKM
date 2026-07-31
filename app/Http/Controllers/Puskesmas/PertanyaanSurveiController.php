<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Models\PertanyaanSurvei;
use App\Models\UnsurPelayanan;
use Illuminate\Http\Request;
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
        $urutanBerikutnya = (PertanyaanSurvei::where('puskesmas_id', Auth::user()->puskesmas_id)->max('urutan') ?? 0) + 1;

        return view('puskesmas.pertanyaan.create', compact('daftarUnsur', 'urutanBerikutnya'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'unsur_pelayanan_id' => ['nullable', 'exists:unsur_pelayanan,id'],
            'teks_pertanyaan' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

        PertanyaanSurvei::create([
            'puskesmas_id' => Auth::user()->puskesmas_id,
            'unsur_pelayanan_id' => $data['unsur_pelayanan_id'] ?: null,
            'teks_pertanyaan' => $data['teks_pertanyaan'],
            'urutan' => $data['urutan'],
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

        return view('puskesmas.pertanyaan.edit', compact('pertanyaan', 'daftarUnsur'));
    }

    public function update(Request $request, PertanyaanSurvei $pertanyaan)
    {
        $this->pastikanSatuUnit($pertanyaan);

        $data = $request->validate([
            'unsur_pelayanan_id' => ['nullable', 'exists:unsur_pelayanan,id'],
            'teks_pertanyaan' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['unsur_pelayanan_id'] = $data['unsur_pelayanan_id'] ?: null;
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
     * Pastikan admin-puskesmas cuma bisa kelola pertanyaan milik unitnya sendiri,
     * meskipun ID pertanyaan unit lain ditebak lewat URL.
     */
    private function pastikanSatuUnit(PertanyaanSurvei $pertanyaan): void
    {
        if ($pertanyaan->puskesmas_id !== Auth::user()->puskesmas_id) {
            throw new AccessDeniedHttpException('Pertanyaan ini bukan milik unit Anda.');
        }
    }
}
