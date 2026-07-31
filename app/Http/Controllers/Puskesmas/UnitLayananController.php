<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Models\UnitLayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UnitLayananController extends Controller
{
    public function index()
    {
        $daftarUnitLayanan = UnitLayanan::where('puskesmas_id', Auth::user()->puskesmas_id)
            ->orderBy('nama')
            ->get();

        return view('puskesmas.unit-layanan.index', compact('daftarUnitLayanan'));
    }

    public function create()
    {
        return view('puskesmas.unit-layanan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        UnitLayanan::create([
            'puskesmas_id' => Auth::user()->puskesmas_id,
            'nama' => $data['nama'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('puskesmas.unit-layanan.index')
            ->with('success', 'Unit layanan berhasil ditambahkan.');
    }

    public function edit(UnitLayanan $unit_layanan)
    {
        $this->pastikanSatuUnit($unit_layanan);

        return view('puskesmas.unit-layanan.edit', ['unitLayanan' => $unit_layanan]);
    }

    public function update(Request $request, UnitLayanan $unit_layanan)
    {
        $this->pastikanSatuUnit($unit_layanan);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $unit_layanan->update($data);

        return redirect()
            ->route('puskesmas.unit-layanan.index')
            ->with('success', 'Unit layanan berhasil diperbarui.');
    }

    public function destroy(UnitLayanan $unit_layanan)
    {
        $this->pastikanSatuUnit($unit_layanan);

        if ($unit_layanan->surveiJawaban()->exists()) {
            return redirect()
                ->route('puskesmas.unit-layanan.index')
                ->with('error', 'Unit layanan ini sudah dipakai di data survei, nonaktifkan saja daripada dihapus.');
        }

        $unit_layanan->delete();

        return redirect()
            ->route('puskesmas.unit-layanan.index')
            ->with('success', 'Unit layanan berhasil dihapus.');
    }

    private function pastikanSatuUnit(UnitLayanan $unitLayanan): void
    {
        if ($unitLayanan->puskesmas_id !== Auth::user()->puskesmas_id) {
            throw new AccessDeniedHttpException('Unit layanan ini bukan milik unit Anda.');
        }
    }
}
