<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dinkes\StoreUnsurPelayananRequest;
use App\Http\Requests\Dinkes\UpdateUnsurPelayananRequest;
use App\Models\UnsurPelayanan;

class UnsurPelayananController extends Controller
{
    public function index()
    {
        $daftarUnsur = UnsurPelayanan::orderBy('urutan')->get();

        return view('dinkes.unsur-pelayanan.index', compact('daftarUnsur'));
    }

    public function create()
    {
        $urutanBerikutnya = (UnsurPelayanan::max('urutan') ?? 0) + 1;

        return view('dinkes.unsur-pelayanan.create', compact('urutanBerikutnya'));
    }

    public function store(StoreUnsurPelayananRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = true;

        UnsurPelayanan::create($data);

        return redirect()
            ->route('dinkes.unsur-pelayanan.index')
            ->with('success', 'Unsur pelayanan baru berhasil ditambahkan.');
    }

    public function edit(UnsurPelayanan $unsur_pelayanan)
    {
        return view('dinkes.unsur-pelayanan.edit', ['unsur' => $unsur_pelayanan]);
    }

    public function update(UpdateUnsurPelayananRequest $request, UnsurPelayanan $unsur_pelayanan)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $unsur_pelayanan->update($data);

        return redirect()
            ->route('dinkes.unsur-pelayanan.index')
            ->with('success', 'Unsur pelayanan berhasil diperbarui.');
    }

    public function destroy(UnsurPelayanan $unsur_pelayanan)
    {
        // cegah hapus kalau sudah dipakai/dikaitkan oleh pertanyaan survei di puskesmas manapun,
        // supaya histori nilai SKM tidak rusak dan mapping puskesmas tidak jadi anak yatim
        if ($unsur_pelayanan->pertanyaanSurvei()->exists()) {
            return redirect()
                ->route('dinkes.unsur-pelayanan.index')
                ->with('error', 'Unsur ini sudah dipakai di pertanyaan survei salah satu unit, nonaktifkan saja daripada dihapus.');
        }

        $unsur_pelayanan->delete();

        return redirect()
            ->route('dinkes.unsur-pelayanan.index')
            ->with('success', 'Unsur pelayanan berhasil dihapus.');
    }
}
