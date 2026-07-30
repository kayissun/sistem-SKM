<?php

namespace App\Http\Controllers\Puskesmas;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PetugasController extends Controller
{
    public function index()
    {
        $daftarPetugas = User::where('puskesmas_id', Auth::user()->puskesmas_id)
            ->where('id', '!=', Auth::id()) // jangan tampilkan akun admin sendiri di daftar petugas
            ->orderBy('name')
            ->paginate(10);

        return view('puskesmas.petugas.index', compact('daftarPetugas'));
    }

    public function create()
    {
        return view('puskesmas.petugas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $petugas = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'puskesmas_id' => Auth::user()->puskesmas_id,
        ]);
        $petugas->assignRole('petugas');

        Password::sendResetLink(['email' => $petugas->email]);

        return redirect()
            ->route('puskesmas.petugas.index')
            ->with('success', "Akun petugas berhasil dibuat. Link untuk membuat password sudah dikirim ke {$petugas->email}.");
    }

    public function edit(User $petugas)
    {
        $this->pastikanSatuUnit($petugas);

        return view('puskesmas.petugas.edit', compact('petugas'));
    }

    public function update(Request $request, User $petugas)
    {
        $this->pastikanSatuUnit($petugas);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $petugas->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $petugas->update($data);

        return redirect()
            ->route('puskesmas.petugas.index')
            ->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(User $petugas)
    {
        $this->pastikanSatuUnit($petugas);

        $petugas->update(['is_active' => false]);

        return redirect()
            ->route('puskesmas.petugas.index')
            ->with('success', 'Akun petugas dinonaktifkan.');
    }

    /**
     * Cegah admin-puskesmas mengedit/menghapus akun milik unit lain
     * lewat manipulasi URL (mis. /puskesmas/petugas/5/edit).
     */
    private function pastikanSatuUnit(User $petugas): void
    {
        if ($petugas->puskesmas_id !== Auth::user()->puskesmas_id) {
            throw new AccessDeniedHttpException('Anda tidak punya akses ke akun ini.');
        }
    }
}
