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
use Illuminate\Support\Facades\Storage;

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

        // Hitung SKM periode sebelumnya untuk perbandingan capaian
        $hasilSebelumnya = null;
        if ($puskesmas && $periode) {
            $periodeSebelumnya = PeriodeSurvei::where('tanggal_mulai', '<', $periode->tanggal_mulai)
                ->orderByDesc('tanggal_mulai')
                ->first();
            if ($periodeSebelumnya) {
                $hasilSebelumnya = $service->hitung($puskesmas, $periodeSebelumnya);
            }
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

        $tahunTersedia = TindakLanjut::where('puskesmas_id', $puskesmas->id)
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $unsurAktif = UnsurPelayanan::aktif()->get();

        return view('puskesmas.tindak-lanjut.index', compact(
            'puskesmas', 'periode', 'daftarPeriode', 'hasil', 'hasilSebelumnya',
            'tindakLanjuts', 'unsurAktif',
            'triwulan', 'tahun', 'tahunTersedia'
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

        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->get();
        $unsurAktif = UnsurPelayanan::aktif()->get();

        // Tentukan triwulan & tahun default dari periode survei (jika ada) atau tanggal saat ini
        $twDefault = (int) ceil(now()->month / 3);
        $thDefault = (int) now()->year;
        if ($periode && $periode->tanggal_mulai) {
            $twDefault = (int) ceil($periode->tanggal_mulai->month / 3);
            $thDefault = (int) $periode->tanggal_mulai->year;
        }

        $triwulanDipilih = $request->integer('triwulan', $twDefault);
        $tahunDipilih = $request->integer('tahun', $thDefault);

        // Unsur yang sudah punya TL di periode (triwulan & tahun) ini
        $unsurSudahAda = TindakLanjut::where('puskesmas_id', $puskesmas->id)
            ->where('triwulan', $triwulanDipilih)
            ->where('tahun', $tahunDipilih)
            ->pluck('unsur_pelayanan_id');

        $selectedKode = $request->input('unsur');
        $selectedUnsur = $selectedKode ? $unsurAktif->firstWhere('kode', $selectedKode) : null;
        $selectedUnsurId = old('unsur_pelayanan_id', $selectedUnsur?->id);

        return view('puskesmas.tindak-lanjut.create', compact(
            'puskesmas', 'periode', 'daftarPeriode', 'hasil', 'unsurAktif',
            'unsurSudahAda', 'triwulanDipilih', 'tahunDipilih',
            'selectedUnsur', 'selectedUnsurId'
        ));
    }

    /**
     * Simpan Tindak Lanjut baru.
     */
    public function store(Request $request, SkmCalculatorService $service)
    {
        $validated = $request->validate([
            'unsur_pelayanan_id' => 'required|exists:unsur_pelayanan,id',
            'triwulan'           => 'required|integer|in:1,2,3,4',
            'tahun'              => 'required|integer|min:2020|max:2030',
            'tindakan_perbaikan' => 'required|string|min:10',
            'foto'               => 'nullable|array|max:5',
            'foto.*'             => 'image|mimes:jpg,jpeg,png|max:2048',
            'periode_survei_id'  => 'nullable|exists:periode_survei,id',
        ]);

        $puskesmas = Auth::user()->puskesmas;

        // Cek duplikat per puskesmas, unsur, triwulan, tahun
        $existing = TindakLanjut::where('puskesmas_id', $puskesmas->id)
            ->where('unsur_pelayanan_id', $validated['unsur_pelayanan_id'])
            ->where('triwulan', $validated['triwulan'])
            ->where('tahun', $validated['tahun'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'unsur_pelayanan_id' => 'Tindak lanjut untuk unsur ini pada triwulan tersebut sudah ada.',
            ])->withInput();
        }

        // Auto-fill nilai_kondisi dari hasil SKM periode terpilih (jika ada)
        $nilaiKondisi = null;
        if (!empty($validated['periode_survei_id'])) {
            $periode = PeriodeSurvei::find($validated['periode_survei_id']);
            if ($periode) {
                $hasil = $service->hitung($puskesmas, $periode);
                $unsur = UnsurPelayanan::find($validated['unsur_pelayanan_id']);
                if ($unsur && isset($hasil['per_unsur'][$unsur->kode])) {
                    $nilaiKondisi = $hasil['per_unsur'][$unsur->kode]['nrr_skala_100'] ?? null;
                }
            }
        }

        // Upload foto kondisi awal
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $fotoPaths[] = $file->store('tindak-lanjut/rencana', 'public');
            }
        }

        TindakLanjut::create([
            'puskesmas_id'       => $puskesmas->id,
            'unsur_pelayanan_id' => $validated['unsur_pelayanan_id'],
            'triwulan'           => $validated['triwulan'],
            'tahun'              => $validated['tahun'],
            'nilai_kondisi'      => $nilaiKondisi,
            'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
            'foto'               => $fotoPaths,
            'status'             => TindakLanjut::STATUS_DRAFT,
        ]);

        return redirect()->route('puskesmas.tindak-lanjut.index')
            ->with('success', 'Rencana tindak lanjut berhasil disimpan sebagai draft.');
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
            'foto_baru'          => 'nullable|array|max:5',
            'foto_baru.*'        => 'image|mimes:jpg,jpeg,png|max:2048',
            'foto_hapus'         => 'nullable|array',
            'foto_hapus.*'       => 'string',
        ]);

        // Upload foto baru
        $fotoExisting = $tindakLanjut->foto ?? [];
        $fotoHapus = $validated['foto_hapus'] ?? [];
        $fotoBaru = [];

        if ($request->hasFile('foto_baru')) {
            foreach ($request->file('foto_baru') as $file) {
                $fotoBaru[] = $file->store('tindak-lanjut/rencana', 'public');
            }
        }

        // Hapus fisik foto yang ditandai hapus
        foreach ($fotoHapus as $p) {
            Storage::disk('public')->delete($p);
        }

        // Gabung: hapus yang dipilih, tambah yang baru
        $fotoAkhir = array_values(array_diff($fotoExisting, $fotoHapus));
        $fotoAkhir = array_merge($fotoAkhir, $fotoBaru);

        $tindakLanjut->update([
            'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
            'foto'               => $fotoAkhir,
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

        foreach ($tindakLanjut->foto ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $tindakLanjut->delete();

        return redirect()->route('puskesmas.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil dihapus.');
    }

    /**
     * Form tambah progress / dokumentasi kegiatan perbaikan.
     */
    public function addProgress(TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403
        );

        $tindakLanjut->load('unsurPelayanan', 'progress.createdBy');

        return view('puskesmas.tindak-lanjut.add-progress', compact('tindakLanjut'));
    }

    /**
     * Simpan progress dokumentasi kegiatan perbaikan (hanya foto + keterangan).
     */
    public function storeProgress(Request $request, TindakLanjut $tindakLanjut)
    {
        $user = Auth::user();

        abort_unless(
            $tindakLanjut->puskesmas_id === $user->puskesmas_id,
            403
        );

        $validated = $request->validate([
            'foto'       => 'required|array|min:1|max:10',
            'foto.*'     => 'image|mimes:jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $fotoPaths[] = $file->store('tindak-lanjut/progress', 'public');
            }
        }

        $tindakLanjut->progress()->create([
            'foto'       => $fotoPaths,
            'keterangan' => $validated['keterangan'] ?? null,
            'created_by' => $user->id,
        ]);

        return redirect()->route('puskesmas.tindak-lanjut.show', $tindakLanjut)
            ->with('success', 'Dokumentasi progres berhasil ditambahkan.');
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

        $tindakLanjut->load(['unsurPelayanan', 'progress.createdBy', 'verifiedBy']);

        return view('puskesmas.tindak-lanjut.show', compact('tindakLanjut'));
    }
}