<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dinkes\StoreUnsurPelayananRequest;
use App\Http\Requests\Dinkes\UpdateUnsurPelayananRequest;
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

    public function aksiMassal(Request $request)
    {
        $request->validate([
            'dipilih' => ['required', 'array', 'min:1'],
            'dipilih.*' => ['exists:unsur_pelayanan,id'],
            'aksi' => ['required', 'in:nonaktifkan,hapus'],
        ]);

        $daftar = UnsurPelayanan::whereIn('id', $request->input('dipilih'))->get();

        if ($request->input('aksi') === 'nonaktifkan') {
            UnsurPelayanan::whereIn('id', $daftar->pluck('id'))->update(['is_active' => false]);

            return redirect()
                ->route('dinkes.unsur-pelayanan.index')
                ->with('success', $daftar->count() . ' unsur berhasil dinonaktifkan.');
        }

        $berhasilDihapus = 0;
        $dilewati = [];

        foreach ($daftar as $unsur) {
            if ($unsur->pertanyaanSurvei()->exists()) {
                $dilewati[] = $unsur->kode;
                $unsur->update(['is_active' => false]);
                continue;
            }

            $unsur->delete();
            $berhasilDihapus++;
        }

        $pesan = "{$berhasilDihapus} unsur berhasil dihapus permanen.";
        if (! empty($dilewati)) {
            $pesan .= ' Unsur berikut sudah dipakai di pertanyaan survei, jadi cuma dinonaktifkan (tidak dihapus): ' . implode(', ', $dilewati) . '.';
        }

        return redirect()
            ->route('dinkes.unsur-pelayanan.index')
            ->with($berhasilDihapus > 0 ? 'success' : 'error', $pesan);
    }
}
