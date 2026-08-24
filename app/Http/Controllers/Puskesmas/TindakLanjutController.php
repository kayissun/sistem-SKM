<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use App\Models\TindakLanjut;
use App\Models\TindakLanjutProgress;
use App\Models\UnsurPelayanan;
use App\Services\SkmCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TindakLanjutController extends Controller
{
    /**
     * Halaman utama Tindak Lanjut — tampilkan daftar TL + analisis unsur lemah.
     */
    public function index(Request $request, SkmCalculatorService $service)
    {
        $user = Auth::user();
        $puskesmas = $user->puskesmas;

        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();
        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');
        $periode = $periodeId ? PeriodeSurvei::find($periodeId) : null;

        // Hitung SKM per unsur untuk periode ini
        $hasil = null;
        if ($puskesmas && $periode) {
            $hasil = $service->hitung($puskesmas, $periode);
        }

        // Data TL milik puskesmas ini
        $triwulan = $request->integer('triwulan');
        $tahun = $request->integer('tahun');

        $queryTl = TindakLanjut::where('puskesmas_id', $puskesmas->id)
            ->with('unsurPelayanan', 'progress');

        if ($triwulan) {
            $queryTl->where('triwulan', $triwulan);
        }
        if ($tahun) {
            $queryTl->where('tahun', $tahun);
        }

        $tindakLanjuts = $queryTl->orderByDesc('tahun')
            ->orderByDesc('triwulan')
            ->get();

        $unsurAktif = UnsurPelayanan::aktif()->get();

        return view('puskesmas.tindak-lanjut.index', compact(
            'puskesmas', 'periode', 'daftarPeriode', 'hasil',
            'tindakLanjuts', 'unsurAktif',
            'triwulan', 'tahun'
        ));
    }

    /**
     * Form buat Tindak Lanjut baru.
     */
    public function create(Request $request, SkmCalculatorService $service)
    {
        $user = Auth::user();
        $puskesmas = $user->puskesmas;

        $periodeId = $request->integer('periode_survei_id')
            ?: PeriodeSurvei::where('is_active', true)->value('id');
        $periode = $periodeId ? PeriodeSurvei::find($periodeId) : null;

        $hasil = null;
        if ($puskesmas && $periode) {
            $hasil = $service->hitung($puskesmas, $periode);
        }

        $unsurAktif = UnsurPelayanan::aktif()->get();

        // Unsur yang sudah punya TL di periode ini
        $unsurSudahAda = TindakLanjut::where('puskesmas_id', $puskesmas->id)
            ->where('triwulan', $request->integer('triwulan', 1))
            ->where('tahun', $request->integer('tahun', date('Y')))
            ->pluck('unsur_pelayanan_id');

        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();

        return view('puskesmas.tindak-lanjut.create', compact(
            'puskesmas', 'periode', 'hasil', 'unsurAktif',
            'unsurSudahAda', 'daftarPeriode'
        ));
    }

    /**
     * Simpan Tindak Lanjut baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unsur_pelayanan_id' => 'required|exists:unsur_pelayanan,id',
            'triwulan' => 'required|integer|in:1,2,3,4',
            'tahun' => 'required|integer|min:2020|max:2030',
            'nilai_kondisi' => 'nullable|numeric|min:0|max:100',
            'tindakan_perbaikan' => 'required|string|min:10',
            'bukti' => 'nullable|string|max:500',
            'foto' => 'nullable|array|max:5',
            'foto.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $puskesmas = Auth::user()->puskesmas;

        // Cek duplikat
        $existing = TindakLanjut::where('puskesmas_id', $puskesmas->id)
            ->where('unsur_pelayanan_id', $validated['unsur_pelayanan_id'])
            ->where('triwulan', $validated['triwulan'])
            ->where('tahun', $validated['tahun'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'unsur_pelayanan_id' => 'Tindak lanjut untuk unsur ini pada periode tersebut sudah ada.',
            ])->withInput();
        }

        // Upload foto
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $fotoPaths[] = $file->store('tindak-lanjut', 'public');
            }
        }

        TindakLanjut::create([
            ...$validated,
            'puskesmas_id' => $puskesmas->id,
            'foto' => $fotoPaths,
            'status' => TindakLanjut::STATUS_DRAFT,
        ]);

        return redirect()->route('puskesmas.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil disimpan sebagai draft.');
    }

    /**
     * Form edit Tindak Lanjut.
     */
    public function edit(TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403, 'Anda tidak memiliki akses ke tindak lanjut ini.'
        );

        abort_unless($tindakLanjut->isEditable(), 400, 'Tindak lanjut tidak dapat diedit.');

        $tindakLanjut->load('unsurPelayanan');
        $unsurAktif = UnsurPelayanan::aktif()->get();

        return view('puskesmas.tindak-lanjut.edit', compact('tindakLanjut', 'unsurAktif'));
    }

    /**
     * Update Tindak Lanjut.
     */
    public function update(Request $request, TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403
        );

        abort_unless($tindakLanjut->isEditable(), 400);

        $validated = $request->validate([
            'tindakan_perbaikan' => 'required|string|min:10',
            'bukti' => 'nullable|string|max:500',
            'foto_baru' => 'nullable|array|max:5',
            'foto_baru.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'foto_hapus' => 'nullable|array',
            'foto_hapus.*' => 'string',
        ]);

        // Upload foto baru
        $fotoExisting = $tindakLanjut->foto ?? [];
        $fotoHapus = $validated['foto_hapus'] ?? [];
        $fotoBaru = [];

        if ($request->hasFile('foto_baru')) {
            foreach ($request->file('foto_baru') as $file) {
                $fotoBaru[] = $file->store('tindak-lanjut', 'public');
            }
        }

        // Gabung: hapus yang dipilih, tambah yang baru
        $fotoAkhir = array_values(array_diff($fotoExisting, $fotoHapus));
        $fotoAkhir = array_merge($fotoAkhir, $fotoBaru);

        $tindakLanjut->update([
            'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
            'bukti' => $validated['bukti'] ?? null,
            'foto' => $fotoAkhir,
        ]);

        return redirect()->route('puskesmas.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil diperbarui.');
    }

    /**
     * Kirim (submit) Tindak Lanjut ke Dinkes.
     */
    public function submit(TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403
        );

        abort_unless($tindakLanjut->isEditable(), 400);

        $tindakLanjut->update([
            'status' => TindakLanjut::STATUS_SUBMITTED,
        ]);

        return redirect()->route('puskesmas.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil dikirim ke Dinkes.');
    }

    /**
     * Hapus Tindak Lanjut (hanya draft).
     */
    public function destroy(TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403
        );

        abort_unless($tindakLanjut->status === TindakLanjut::STATUS_DRAFT, 400, 'Hanya draft yang bisa dihapus.');

        $tindakLanjut->delete();

        return redirect()->route('puskesmas.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil dihapus.');
    }

    /**
     * Form tambah progress / capaian triwulan.
     */
    public function addProgress(TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403
        );

        $tindakLanjut->load('unsurPelayanan', 'progress');

        return view('puskesmas.tindak-lanjut.add-progress', compact('tindakLanjut'));
    }

    /**
     * Simpan progress / capaian triwulan.
     */
    public function storeProgress(Request $request, TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403
        );

        $validated = $request->validate([
            'triwulan_target' => 'required|integer|in:1,2,3,4',
            'tahun_target' => 'required|integer|min:2020|max:2030',
            'nilai_akhir' => 'required|numeric|min:0|max:100',
            'tercapai' => 'required|boolean',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        // Cek duplikat progress
        $existing = TindakLanjutProgress::where('tindak_lanjut_id', $tindakLanjut->id)
            ->where('triwulan_target', $validated['triwulan_target'])
            ->where('tahun_target', $validated['tahun_target'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'triwulan_target' => 'Progress untuk triwulan ini sudah ada.',
            ])->withInput();
        }

        $tindakLanjut->progress()->create($validated);

        return redirect()->route('puskesmas.tindak-lanjut.index')
            ->with('success', 'Progress capaian berhasil ditambahkan.');
    }

    /**
     * Detail Tindak Lanjut (lihat semua progress).
     */
    public function show(TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403
        );

        $tindakLanjut->load(['unsurPelayanan', 'progress', 'verifiedBy']);

        return view('puskesmas.tindak-lanjut.show', compact('tindakLanjut'));
    }
}