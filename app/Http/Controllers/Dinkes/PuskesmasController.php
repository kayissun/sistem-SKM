<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Models\Puskesmas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PuskesmasController extends Controller
{
    public function index()
    {
        $daftarPuskesmas = Puskesmas::withCount('users')
            ->orderBy('nama')
            ->paginate(10);

        return view('dinkes.puskesmas.index', compact('daftarPuskesmas'));
    }

    public function create()
    {
        return view('dinkes.puskesmas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:puskesmas,rsu'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'admin_nama' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'unique:users,email'],
        ]);

        $puskesmas = Puskesmas::create([
            'nama' => $data['nama'],
            'slug' => Str::slug($data['nama']) . '-' . Str::random(4),
            'jenis' => $data['jenis'],
            'alamat' => $data['alamat'] ?? null,
            'kecamatan' => $data['kecamatan'] ?? null,
            'no_telepon' => $data['no_telepon'] ?? null,
            'is_active' => true,
        ]);

        // Sengaja TIDAK auto-isi pertanyaan survei apa pun — admin-puskesmas mengatur
        // kuesionernya sendiri dari nol lewat menu "Pertanyaan Survei" (termasuk memetakan
        // ke 9 unsur baku U1-U9 satu per satu sesuai kebutuhan unit masing-masing).

        // password acak sebagai placeholder di database, tidak pernah diberitahu ke siapa pun;
        // admin akan mengatur passwordnya sendiri lewat link yang dikirim ke email
        $admin = User::create([
            'name' => $data['admin_nama'],
            'email' => $data['admin_email'],
            'password' => Hash::make(Str::random(32)),
            'puskesmas_id' => $puskesmas->id,
        ]);
        $admin->assignRole('admin-puskesmas');

        Password::sendResetLink(['email' => $admin->email]);

        return redirect()
            ->route('dinkes.puskesmas.index')
            ->with('success', "Unit \"{$puskesmas->nama}\" berhasil dibuat. Link untuk membuat password sudah dikirim ke {$admin->email}.");
    }

    public function edit(Puskesmas $puskesma)
    {
        return view('dinkes.puskesmas.edit', ['puskesmas' => $puskesma]);
    }

    public function update(Request $request, Puskesmas $puskesma)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:puskesmas,rsu'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $puskesma->update($data);

        return redirect()
            ->route('dinkes.puskesmas.index')
            ->with('success', 'Data unit berhasil diperbarui.');
    }

    public function destroy(Puskesmas $puskesma)
    {
        // soft-disable lebih aman daripada hard delete supaya histori survei tidak hilang
        $puskesma->update(['is_active' => false]);

        return redirect()
            ->route('dinkes.puskesmas.index')
            ->with('success', 'Unit dinonaktifkan. Data histori survei tetap tersimpan.');
    }
}
