<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Puskesmas\PertanyaanBulkActionRequest;
use App\Http\Requests\Puskesmas\PertanyaanSurveiRequest;
use App\Models\PertanyaanSurvei;
use App\Models\UnsurPelayanan;
use App\Support\PresetLabelSkala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        $unsurTerpakai = $daftarPertanyaan->where('is_active', true)->pluck('unsur_pelayanan_id')->filter();
        $unsurBelumAda = UnsurPelayanan::aktif()->get()->reject(fn ($unsur) => $unsurTerpakai->contains($unsur->id));

        return view('puskesmas.pertanyaan.index', compact('daftarPertanyaan', 'unsurBelumAda'));
    }

    public function create(Request $request)
    {
        $puskesmasId = Auth::user()->puskesmas_id;

        $daftarPertanyaan = PertanyaanSurvei::with('unsurPelayanan')
            ->where('puskesmas_id', $puskesmasId)
            ->orderBy('urutan')
            ->get();

        $daftarUnsur = UnsurPelayanan::aktif()->get();
        $presetLabel = PresetLabelSkala::daftar();
        $formHeaderImageUrl = Auth::user()->puskesmas->formHeaderImageUrl();
        $pisahHalaman = Auth::user()->puskesmas->form_pisah_halaman;

        return view('puskesmas.pertanyaan.create', compact('daftarPertanyaan', 'daftarUnsur', 'presetLabel', 'formHeaderImageUrl', 'pisahHalaman'));
    }

    public function store(PertanyaanSurveiRequest $request)
    {
        $puskesmasId = Auth::user()->puskesmas_id;
        $maxUrutan = PertanyaanSurvei::where('puskesmas_id', $puskesmasId)->max('urutan') ?? 0;

        $data = $request->validated();
        $data['puskesmas_id'] = $puskesmasId;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['urutan'] = $request->input('urutan', $maxUrutan + 1);

        if ($request->hasFile('header_image')) {
            $data['header_image'] = $request->file('header_image')->store('pertanyaan-header', 'public');
        }

        $pertanyaan = PertanyaanSurvei::create($data);
        $pertanyaan->load('unsurPelayanan');

        if ($request->wantsJson()) {
            $arr = $pertanyaan->toArray();
            $arr['header_image_url'] = $pertanyaan->headerImageUrl();
            return response()->json(['success' => true, 'message' => 'Pertanyaan baru berhasil ditambahkan.', 'data' => $arr]);
        }

        return redirect()->route('puskesmas.pertanyaan.index')->with('success', 'Pertanyaan survei berhasil ditambahkan.');
    }

    public function edit(PertanyaanSurvei $pertanyaan)
    {
        // Alihkan edit ke halaman builder utama
        return redirect()->route('puskesmas.pertanyaan.create');
    }

    public function update(PertanyaanSurveiRequest $request, PertanyaanSurvei $pertanyaan)
    {
        $this->pastikanSatuUnit($pertanyaan);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('header_image')) {
            if ($pertanyaan->header_image) {
                Storage::disk('public')->delete($pertanyaan->header_image);
            }
            $data['header_image'] = $request->file('header_image')->store('pertanyaan-header', 'public');
        }

        $pertanyaan->update($data);
        $pertanyaan->load('unsurPelayanan');

        if ($request->wantsJson()) {
            $arr = $pertanyaan->toArray();
            $arr['header_image_url'] = $pertanyaan->headerImageUrl();
            return response()->json(['success' => true, 'message' => 'Pertanyaan berhasil diperbarui.', 'data' => $arr]);
        }

        return redirect()->route('puskesmas.pertanyaan.index')->with('success', 'Pertanyaan survei berhasil diperbarui.');
    }

    public function destroy(Request $request, PertanyaanSurvei $pertanyaan)
    {
        $this->pastikanSatuUnit($pertanyaan);

        $hasAnswers = $pertanyaan->surveiJawabanDetail()->exists();

        if ($hasAnswers) {
            $pertanyaan->update(['is_active' => false]);
            $msg = 'Pertanyaan sudah memiliki jawaban responden. Status otomatis diubah menjadi Nonaktif.';

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'deactivated' => true, 'message' => $msg]);
            }
            return redirect()->route('puskesmas.pertanyaan.index')->with('error', $msg);
        }

        if ($pertanyaan->header_image) {
            Storage::disk('public')->delete($pertanyaan->header_image);
        }

        $pertanyaan->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Pertanyaan berhasil dihapus permanen.']);
        }

        return redirect()->route('puskesmas.pertanyaan.index')->with('success', 'Pertanyaan survei berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'urutan' => 'required|array',
            'urutan.*' => 'integer|exists:pertanyaan_survei,id',
        ]);

        $puskesmasId = Auth::user()->puskesmas_id;

        foreach ($request->urutan as $index => $id) {
            PertanyaanSurvei::where('id', $id)
                ->where('puskesmas_id', $puskesmasId)
                ->update(['urutan' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan pertanyaan berhasil disimpan.']);
    }

    public function duplikat(PertanyaanSurvei $pertanyaan)
    {
        $this->pastikanSatuUnit($pertanyaan);

        $baru = $pertanyaan->replicate();
        $baru->teks_pertanyaan = $pertanyaan->teks_pertanyaan . ' (Salinan)';
        $baru->urutan = $pertanyaan->urutan + 1;
        $baru->save();
        $baru->load('unsurPelayanan');

        PertanyaanSurvei::where('puskesmas_id', Auth::user()->puskesmas_id)
            ->where('id', '!=', $baru->id)
            ->where('urutan', '>=', $baru->urutan)
            ->increment('urutan');

        $arr = $baru->toArray();
        $arr['header_image_url'] = $baru->headerImageUrl();

        return response()->json(['success' => true, 'message' => 'Pertanyaan berhasil diduplikat.', 'data' => $arr]);
    }

    public function updateHeaderImage(Request $request, PertanyaanSurvei $pertanyaan)
    {
        $this->pastikanSatuUnit($pertanyaan);

        $request->validate([
            'header_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hapus_header_image' => 'nullable|boolean',
        ]);

        if ($request->boolean('hapus_header_image')) {
            if ($pertanyaan->header_image) {
                Storage::disk('public')->delete($pertanyaan->header_image);
                $pertanyaan->update(['header_image' => null]);
            }
        } elseif ($request->hasFile('header_image')) {
            if ($pertanyaan->header_image) {
                Storage::disk('public')->delete($pertanyaan->header_image);
            }
            $path = $request->file('header_image')->store('pertanyaan-header', 'public');
            $pertanyaan->update(['header_image' => $path]);
        }

        $arr = $pertanyaan->fresh()->toArray();
        $arr['header_image_url'] = $pertanyaan->headerImageUrl();

        return response()->json(['success' => true, 'message' => 'Gambar header berhasil diperbarui.', 'data' => $arr]);
    }

    public function togglePisahHalaman(Request $request)
    {
        $request->validate(['pisah_halaman' => ['required', 'boolean']]);

        Auth::user()->puskesmas->update([
            'form_pisah_halaman' => $request->boolean('pisah_halaman'),
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->boolean('pisah_halaman')
                ? 'Form survei akan dipisah per halaman (Data Diri → Pertanyaan).'
                : 'Form survei dalam satu halaman penuh.',
        ]);
    }

    public function uploadFormHeaderImage(Request $request)
    {
        $request->validate([
            'form_header_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $puskesmas = Auth::user()->puskesmas;

        if ($puskesmas->form_header_image) {
            Storage::disk('public')->delete($puskesmas->form_header_image);
        }

        $path = $request->file('form_header_image')->store('form-header', 'public');
        $puskesmas->update(['form_header_image' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Gambar identitas form berhasil diunggah.',
            'data' => ['form_header_image_url' => $puskesmas->formHeaderImageUrl()],
        ]);
    }

    public function hapusFormHeaderImage()
    {
        $puskesmas = Auth::user()->puskesmas;

        if ($puskesmas->form_header_image) {
            Storage::disk('public')->delete($puskesmas->form_header_image);
            $puskesmas->update(['form_header_image' => null]);
        }

        return response()->json(['success' => true, 'message' => 'Gambar identitas form dihapus.']);
    }

    public function aksiMassal(PertanyaanBulkActionRequest $request)
    {
        $daftar = PertanyaanSurvei::where('puskesmas_id', Auth::user()->puskesmas_id)
            ->whereIn('id', $request->validated('dipilih'))
            ->get();

        $berhasilDihapus = 0;
        $dilewati = [];

        foreach ($daftar as $pertanyaan) {
            if ($pertanyaan->surveiJawabanDetail()->exists()) {
                $dilewati[] = $pertanyaan->teks_pertanyaan;
                $pertanyaan->update(['is_active' => false]);
                continue;
            }

            if ($pertanyaan->header_image) {
                Storage::disk('public')->delete($pertanyaan->header_image);
            }

            $pertanyaan->delete();
            $berhasilDihapus++;
        }

        $pesan = "{$berhasilDihapus} pertanyaan berhasil dihapus permanen.";
        if (! empty($dilewati)) {
            $pesan .= ' Pertanyaan yang sudah punya jawaban responden dinonaktifkan otomatis: ' . implode(', ', array_slice($dilewati, 0, 3)) . '.';
        }

        return redirect()->route('puskesmas.pertanyaan.index')->with($berhasilDihapus > 0 ? 'success' : 'error', $pesan);
    }

    private function pastikanSatuUnit(PertanyaanSurvei $pertanyaan): void
    {
        if ($pertanyaan->puskesmas_id !== Auth::user()->puskesmas_id) {
            throw new AccessDeniedHttpException('Pertanyaan ini bukan milik unit Anda.');
        }
    }
}