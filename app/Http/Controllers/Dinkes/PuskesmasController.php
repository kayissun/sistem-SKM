<?php
namespace App\Http\Controllers\Dinkes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dinkes\StorePuskesmasRequest;
use App\Http\Requests\Dinkes\UpdatePuskesmasRequest;
use App\Models\Puskesmas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PuskesmasController extends Controller
{
    public function index()
    {
        $daftarPuskesmas = Puskesmas::with(['admin' => fn($q) => $q->select('id', 'puskesmas_id', 'email')])
            ->whereIn('jenis', ['puskesmas', 'rsu']) // unit Dinas Kesehatan sendiri tidak muncul di sini
            ->orderBy('nama')
            ->paginate(10);

        return view('dinkes.puskesmas.index', compact('daftarPuskesmas'));
    }

    public function create()
    {
        return view('dinkes.puskesmas.create');
    }

    public function store(StorePuskesmasRequest $request)
    {
        $data = $request->validated();

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

    public function update(UpdatePuskesmasRequest $request, Puskesmas $puskesma)
    {
        $data = $request->validated();

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

    public function aksiMassal(Request $request)
    {
        $request->validate([
            'dipilih' => ['required', 'array', 'min:1'],
            'dipilih.*' => ['exists:puskesmas,id'],
            'aksi' => ['required', 'in:nonaktifkan,hapus'],
        ]);

        $daftar = Puskesmas::whereIn('id', $request->input('dipilih'))->get();

        if ($request->input('aksi') === 'nonaktifkan') {
            Puskesmas::whereIn('id', $daftar->pluck('id'))->update(['is_active' => false]);

            return redirect()
                ->route('dinkes.puskesmas.index')
                ->with('success', $daftar->count() . ' unit berhasil dinonaktifkan.');
        }

        $berhasilDihapus = 0;
        $dilewati = [];

        foreach ($daftar as $puskesmas) {
            if ($puskesmas->surveiJawaban()->exists()) {
                $dilewati[] = $puskesmas->nama;
                $puskesmas->update(['is_active' => false]); // fallback aman: nonaktifkan aja
                continue;
            }

            $puskesmas->delete();
            $berhasilDihapus++;
        }

        $pesan = "{$berhasilDihapus} unit berhasil dihapus permanen.";
        if (! empty($dilewati)) {
            $pesan .= ' Unit berikut sudah punya data survei, jadi cuma dinonaktifkan (tidak dihapus): '
                . implode(', ', $dilewati) . '.';
        }

        return redirect()
            ->route('dinkes.puskesmas.index')
            ->with($berhasilDihapus > 0 ? 'success' : 'error', $pesan);
    }
}
