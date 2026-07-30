<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Models\UnsurPelayanan;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:unsur_pelayanan,kode'],
            'pertanyaan' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]);

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

    public function update(Request $request, UnsurPelayanan $unsur_pelayanan)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:unsur_pelayanan,kode,' . $unsur_pelayanan->id],
            'pertanyaan' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $unsur_pelayanan->update($data);

        return redirect()
            ->route('dinkes.unsur-pelayanan.index')
            ->with('success', 'Unsur pelayanan berhasil diperbarui.');
    }

    public function destroy(UnsurPelayanan $unsur_pelayanan)
    {
        // cegah hapus kalau sudah pernah dipakai responden, supaya histori nilai tidak rusak
        if ($unsur_pelayanan->surveiJawabanDetail()->exists()) {
            return redirect()
                ->route('dinkes.unsur-pelayanan.index')
                ->with('error', 'Unsur ini sudah punya data jawaban, nonaktifkan saja daripada dihapus.');
        }

        $unsur_pelayanan->delete();

        return redirect()
            ->route('dinkes.unsur-pelayanan.index')
            ->with('success', 'Unsur pelayanan berhasil dihapus.');
    }
}
