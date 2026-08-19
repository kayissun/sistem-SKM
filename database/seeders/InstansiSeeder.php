<?php

namespace Database\Seeders;

use App\Models\Puskesmas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InstansiSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('DEFAULT_INSTANSI_PASSWORD', 'password');

        foreach ($this->daftarInstansi() as $data) {
            $instansi = Puskesmas::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'nama' => $data['nama'],
                    'jenis' => $data['jenis'],
                    'is_active' => true,
                ]
            );

            $akun = User::firstOrCreate(
                ['email' => "admin@{$data['slug']}"],
                [
                    'name' => "Admin {$data['nama']}",
                    'password' => Hash::make($password),
                    'puskesmas_id' => $instansi->id,
                    'is_active' => true,
                ]
            );

            $akun->update([
                'puskesmas_id' => $instansi->id,
                'is_active' => true,
            ]);
            $akun->syncRoles(['admin-puskesmas']);
        }
    }

    /**
     * Sumber data resmi instansi yang menerima akun admin bawaan.
     * Nilai slug dipakai sekaligus sebagai identitas URL dan domain email akun.
     */
    private function daftarInstansi(): array
    {
        return [
            ['nama' => 'Puskesmas Bener', 'slug' => 'bener.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Cangkrep Purworejo', 'slug' => 'cangkrep-purworejo.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Bayan', 'slug' => 'bayan.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Bragolan', 'slug' => 'bragolan.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Bruno', 'slug' => 'bruno.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Butuh', 'slug' => 'butuh.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Karanggetas Pituruh', 'slug' => 'karanggetas-pituruh.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Ngombol', 'slug' => 'ngombol.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Grabag', 'slug' => 'grabag.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Pituruh', 'slug' => 'pituruh.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Mranti', 'slug' => 'mranti.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Padirejo', 'slug' => 'padirejo.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Purworejo', 'slug' => 'purworejo.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Semawung Daleman Kutoarjo', 'slug' => 'semawung-daleman-kutoarjo.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Sruwohrejo Butuh', 'slug' => 'sruwohrejo-butuh.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Winong Kemiri', 'slug' => 'winong-kemiri.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Banyuurip', 'slug' => 'banyuurip.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Kaligesing', 'slug' => 'kaligesing.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Loano', 'slug' => 'loano.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Sedorokrapyak', 'slug' => 'sedorokrapyak.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Kemiri', 'slug' => 'kemiri.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Gebang', 'slug' => 'gebang.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Bubutan', 'slug' => 'bubutan.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Wirun', 'slug' => 'wirun.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Bagelen', 'slug' => 'bagelen.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Kutoarjo', 'slug' => 'kutoarjo.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'Puskesmas Banyuasin Loano', 'slug' => 'banyuasin-loano.example.com', 'jenis' => 'puskesmas'],
            ['nama' => 'RSUD Dr. Tjitrowardojo', 'slug' => 'tjitrowardojo.example.com', 'jenis' => 'rsu'],
            ['nama' => 'RSUD R.A.A. Tjokronegoro', 'slug' => 'tjokronegoro.example.com', 'jenis' => 'rsu'],
        ];
    }
}