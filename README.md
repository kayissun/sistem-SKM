# Struktur database SKM (Dinkes - Puskesmas/RSU - Responden)

## Cara pasang di project Laravel kamu

1. Copy file-file ini ke folder project Laravel:
   - `database/migrations/*` -> `database/migrations/`
   - `database/seeders/*` -> `database/seeders/`
   - `app/Models/*` -> `app/Models/` (User.php akan menimpa yang lama, cek dulu isi customisasimu)
   - `app/Services/*` -> `app/Services/`

2. Pastikan Spatie Permission sudah publish migration-nya (kalau belum):
   ```
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   ```

3. Jalankan migration (urutan sudah diatur lewat nama file timestamp):
   ```
   php artisan migrate
   ```

4. Jalankan seeder (isi role, permission, 9 master unsur pelayanan, dan akun contoh):
   ```
   php artisan db:seed
   ```

5. Akun contoh setelah seeding:
   - Dinkes: `dinkes@example.test` / `password`
   - Admin Puskesmas: `admin.puskesmas@example.test` / `password`

## Alur data

- `puskesmas` — data instansi (dikelola dinkes)
- `users` — 1 tabel untuk dinkes & admin-puskesmas, dibedakan lewat kolom `puskesmas_id` (null = dinkes) dan role Spatie
- `unsur_pelayanan` — 9 unsur Permenpan RB 14/2017, dikelola dinkes, dipakai bersama semua puskesmas
- `periode_survei` — periode aktif (triwulan/semester), dikelola dinkes
- `survei_jawaban` — 1 baris per responden yang mengisi survei (publik, tanpa login), terikat ke `puskesmas_id` + `periode_survei_id`
- `survei_jawaban_detail` — nilai 1-4 per unsur pelayanan untuk setiap `survei_jawaban`

## Kalkulasi SKM

Logika rumus lama kamu (total, NRR, NRR skala 100, kategori, NRR tertimbang, nilai akhir)
sudah dipindah ke `app/Services/SkmCalculatorService.php`, method `hitung()`.
Tinggal dipanggil dari controller:

```php
$service = new \App\Services\SkmCalculatorService();
$hasil = $service->hitung($puskesmas, $periodeAktif);
```

Untuk rekap gabungan semua puskesmas (khusus dinkes), pakai `hitungGabungan()`.

## Scoping akses (Dinkes vs Admin Puskesmas)

Contoh pola scoping di controller admin:

```php
$jawaban = \App\Models\SurveiJawaban::untukUser(auth()->user())->get();
```

Scope `untukUser()` di model `SurveiJawaban` otomatis mem-filter berdasarkan `puskesmas_id`
milik user yang login, kecuali role-nya `dinkes` (bisa lihat semua).

## Modul Dinkes (superadmin)

File tambahan di paket ini:
- `app/Http/Controllers/Dinkes/*` — Dashboard, Puskesmas, UnsurPelayanan, PeriodeSurvei, Laporan
- `resources/views/dinkes/*` — view Bootstrap untuk semua controller di atas
- `resources/views/layouts/dinkes.blade.php` — layout navbar Bootstrap
- `routes/dinkes.php` — semua route modul dinkes

### Cara pasang

1. Copy semua file ke lokasi yang sama di project Laravel kamu.

2. Daftarkan alias middleware `role` dari Spatie di `bootstrap/app.php` (kalau belum ada):
   ```php
   ->withMiddleware(function (Middleware $middleware) {
       $middleware->alias([
           'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
       ]);
   })
   ```

3. Include route dinkes di `routes/web.php`:
   ```php
   require __DIR__.'/dinkes.php';
   ```

4. Jalankan `php artisan route:list --name=dinkes` untuk memastikan semua route terdaftar.

5. Login pakai akun `dinkes@example.test` / `password` (dari `DemoDataSeeder`), lalu buka `/dinkes/dashboard`.

### Alur fitur

- **Tambah Puskesmas/RSU** otomatis membuat 1 akun `admin-puskesmas` terkait, dengan password acak yang ditampilkan sekali lewat flash message — sebaiknya nanti ditambah fitur kirim email/reset password, bukan ditampilkan di layar produksi.
- **Hapus Puskesmas** sebenarnya soft-disable (`is_active = false`), bukan hard delete, supaya histori survei tidak hilang.
- **Unsur pelayanan** hanya bisa dihapus kalau belum pernah ada jawaban responden yang memakainya; kalau sudah ada, tombol hapus akan menolak dan menyarankan nonaktifkan saja.
- **Periode survei aktif** dijaga cuma satu dalam satu waktu — mencentang "jadikan aktif" otomatis menonaktifkan periode aktif lainnya.
- **Laporan** memakai `SkmCalculatorService` yang sudah dibuat sebelumnya; halaman index untuk rekap semua unit, halaman detail untuk rincian per unsur di satu unit.

## Modul survei publik (responden)

File tambahan:
- `app/Http/Controllers/SurveiPublikController.php` — tanpa middleware auth, bisa diakses siapa saja
- `resources/views/survei/*` — form + halaman terima kasih
- `resources/views/layouts/publik.blade.php` — layout sederhana tanpa navbar admin
- `routes/survei.php` — route publik

### Cara pasang

1. Copy semua file ke lokasi yang sama.
2. Include di `routes/web.php`:
   ```php
   require __DIR__.'/survei.php';
   ```
3. Ambil link survei tiap unit dari halaman **Dinkes > Puskesmas/RSU**, tombol "Link survei" — formatnya `/survei/{slug-puskesmas}`. Link ini yang nanti dijadikan QR code untuk ditempel di loket pelayanan.

### Perilaku penting

- Kalau belum ada **periode survei aktif**, form tidak akan tampil — responden akan lihat pesan "survei sedang tidak dibuka".
- Kalau puskesmas di-nonaktifkan dinkes (`is_active = false`), link survei-nya otomatis 404.
- Periode yang dipakai saat submit diambil ulang dari server (bukan dari form), supaya kalau dinkes ganti periode aktif saat form sedang dibuka orang lain, datanya tetap konsisten dan tidak bisa dimanipulasi lewat request.
- Semua unsur pelayanan aktif wajib dinilai (skala 1-4, radio button), tidak ada default kosong.
- Data demografis (jenis kelamin, usia, pendidikan, pekerjaan, unit layanan) semuanya opsional.

## Modul admin-Puskesmas / petugas

File tambahan:
- `app/Http/Controllers/Puskesmas/*` — Dashboard, Petugas, Laporan
- `resources/views/puskesmas/*` — view Bootstrap untuk semua controller di atas
- `resources/views/layouts/puskesmas.blade.php` — layout navbar (menu "Petugas" hanya tampil untuk role admin-puskesmas, dijaga pakai `@role('admin-puskesmas')` directive dari Spatie)
- `routes/puskesmas.php` — route modul ini

### Cara pasang

1. Copy semua file ke lokasi yang sama.
2. Include di `routes/web.php`:
   ```php
   require __DIR__.'/puskesmas.php';
   ```
3. Login sebagai admin unit contoh: `admin.puskesmas@example.test` / `password` (dari `DemoDataSeeder`), otomatis diarahkan ke `/puskesmas/dashboard`.

### Pembagian akses admin-puskesmas vs petugas

| Fitur | admin-puskesmas | petugas |
|---|---|---|
| Dashboard unit | ✅ | ❌ (langsung diarahkan ke laporan saat login) |
| Kelola petugas (CRUD) | ✅ | ❌ |
| Lihat laporan unit sendiri | ✅ | ✅ |

Semua query di modul ini otomatis di-scope ke `puskesmas_id` milik user yang login (bukan dari input/URL), termasuk method `pastikanSatuUnit()` di `PetugasController` yang menolak akses (403) kalau ada admin-puskesmas mencoba edit/hapus akun petugas milik unit lain lewat manipulasi URL.

## Export PDF & Excel (dinkes + admin-puskesmas)

File tambahan:
- `app/Exports/LaporanUnsurExport.php` — Excel rincian per unsur (dipakai dinkes-detail & puskesmas-laporan)
- `app/Exports/RekapGabunganExport.php` — Excel rekap semua unit (khusus dinkes)
- `resources/views/exports/laporan-pdf.blade.php` — PDF rincian per unsur
- `resources/views/exports/rekap-gabungan-pdf.blade.php` — PDF rekap semua unit
- Controller `Dinkes\LaporanController` & `Puskesmas\LaporanController` sudah ditambah method `exportPdf*` dan `exportExcel*`
- Route export sudah ditambahkan di `routes/dinkes.php` dan `routes/puskesmas.php`

### Package yang wajib di-install dulu

```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```

Kedua package ini auto-discover service provider-nya sendiri, tidak perlu registrasi manual di `bootstrap/app.php` (Laravel 13 pakai package auto-discovery bawaan Composer).

### Cara pasang

1. Jalankan 2 perintah composer di atas.
2. Copy semua file dari paket ini ke lokasi yang sama (akan menimpa `LaporanController` dinkes & puskesmas yang lama — sudah termasuk semua fitur sebelumnya + export baru).
3. Tidak perlu migration/seeder tambahan untuk fitur ini.

### Yang bisa dites

- **Dinkes > Laporan** (rekap semua unit): tombol "Export PDF" dan "Export Excel" di atas tabel.
- **Dinkes > Laporan > Lihat detail** salah satu unit: tombol export lagi, tapi datanya rincian per unsur unit tsb.
- **Puskesmas > Laporan** (punya sendiri): tombol export yang sama, otomatis cuma data unit yang login.

Kalau nanti muncul error "Class dompdf\Dompdf\Dompdf not found" atau semacamnya, biasanya composer belum selesai/gagal install — cek dulu `composer.json` apakah dua package itu sudah masuk `require`.

## QR code otomatis untuk link survei

File tambahan:
- `app/Http/Controllers/QrCodeController.php` — generate QR sebagai gambar (`tampil`) atau file unduhan (`unduh`)
- Route baru di `routes/survei.php`: `qrcode.tampil` dan `qrcode.unduh`
- Update view: **Dinkes > Puskesmas/RSU** (thumbnail QR + tombol unduh per baris) dan **Puskesmas > Dashboard** (QR besar + tombol unduh)

### Package yang wajib di-install dulu

```bash
composer require endroid/qr-code
```

Package ini murni PHP (pakai GD, bukan Imagick), jadi tidak perlu ekstensi tambahan di server.

### Cara pasang

1. Jalankan composer di atas.
2. Copy semua file ke lokasi yang sama (`routes/survei.php` akan menimpa yang lama — sudah termasuk route survei + QR).
3. Tidak ada migration/seeder tambahan.

### Cara kerja

- QR di-generate on-the-fly setiap request (bukan disimpan sebagai file), jadi kalau slug puskesmas berubah, QR otomatis ikut berubah tanpa perlu regenerate manual.
- QR berisi URL langsung ke halaman survei publik (`/survei/{slug}`), bukan data lain — jadi discan pakai kamera HP biasa langsung buka form survei.
- Kalau puskesmas dinonaktifkan (`is_active = false`), endpoint QR ikut menolak (404) sama seperti halaman survei-nya.

### Yang bisa dites

- **Dinkes > Puskesmas/RSU**: kolom QR muncul di tiap baris, klik "Unduh QR" untuk download PNG resolusi besar (siap cetak/tempel).
- **Puskesmas > Dashboard**: QR besar + tombol unduh, supaya admin unit bisa cetak sendiri tanpa perlu minta ke dinkes.
- Coba scan QR-nya pakai HP, pastikan langsung membuka form survei unit yang benar.

## Keamanan pembuatan akun (link set-password via email)

Perubahan di `PuskesmasController@store` (dinkes) dan `PetugasController@store` (puskesmas):
sebelumnya password sementara ditampilkan langsung di layar, sekarang diganti kirim
**link "buat password"** ke email akun baru, pakai sistem reset password bawaan Breeze
(`Password::sendResetLink()`) — tidak perlu setup notifikasi/mailable baru.

### Supaya email benar-benar terkirim, atur `.env`:

**Untuk testing lokal** (email tidak benar-benar terkirim, cuma dicatat ke log):
```env
MAIL_MAILER=log
```
Setelah tambah puskesmas/petugas baru, buka `storage/logs/laravel.log`, cari baris paling bawah
yang berisi link `reset-password/...` — itu link yang seharusnya diterima admin/petugas lewat email.

**Untuk lihat email beneran di local** (opsional, lebih nyaman daripada baca log), pakai [Mailtrap](https://mailtrap.io) atau `Mailpit` (Laragon versi baru biasanya sudah menyediakan Mailpit):
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

**Untuk production**, ganti ke SMTP asli (Gmail, provider hosting, dsb) sesuai kredensial yang tersedia.

Akun demo dari `DemoDataSeeder` (`dinkes@example.test`, `admin.puskesmas@example.test`) **tidak terpengaruh**
perubahan ini — password-nya tetap `password` seperti biasa karena dibuat langsung lewat seeder, bukan lewat form.

## Proteksi spam di form survei publik

Route `survei.store` (submit jawaban) sudah dipasangi rate limit: maksimal **15 submit per 10 menit per IP**.
Kalau limit ini kelampaui, Laravel otomatis menampilkan halaman error 429 "Too Many Requests" bawaan —
belum ada halaman error kustom untuk ini, cukup memadai untuk cegah spam kasar/bot, tapi kalau butuh pesan
yang lebih ramah untuk responden, itu bisa ditambahkan lewat custom exception handler nanti.

## Checklist keamanan sebelum production

Lihat file **`PRODUCTION_CHECKLIST.md`** dan **`db-user-setup.sql`** di root paket ini — mencakup
konfigurasi `.env`, HTTPS, permission file, dan setup user database dengan hak akses terbatas.
Kerjakan sebelum sistem ini dipakai dengan data sungguhan di 29 unit.

## Audit trail / log aktivitas

File tambahan:
- 4 model (`User`, `Puskesmas`, `UnsurPelayanan`, `PeriodeSurvei`) sudah ditambah trait `LogsActivity`
  dari package `spatie/laravel-activitylog` — otomatis mencatat setiap create/update/delete.
- `app/Http/Controllers/Dinkes/AktivitasController.php` — halaman untuk dinkes melihat log
- `resources/views/dinkes/aktivitas/index.blade.php`
- Route `dinkes.aktivitas.index`, menu "Log Aktivitas" sudah ditambah di navbar dinkes

### Package yang wajib di-install dulu

```bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

### Cara pasang

1. Jalankan 3 perintah di atas (urutannya penting: publish dulu baru migrate).
2. Copy semua file dari paket ini ke lokasi yang sama (akan menimpa 4 model + `routes/dinkes.php` +
   `layouts/dinkes.blade.php` yang lama — sudah termasuk semua fitur sebelumnya + audit trail baru).

### Yang dicatat vs yang tidak

| Dicatat | Tidak dicatat |
|---|---|
| Buat/edit/nonaktifkan Puskesmas | Isi jawaban survei responden (volumenya terlalu tinggi, tidak relevan untuk audit) |
| Buat/edit/hapus Unsur Pelayanan | |
| Buat/edit periode survei | |
| Buat/edit akun user (admin-puskesmas, petugas, dinkes) — **kecuali kolom password**, supaya hash password tidak pernah ikut tercatat di log | |

### Yang bisa dites

- **Dinkes > Log Aktivitas**: coba edit salah satu Puskesmas atau nonaktifkan unsur pelayanan,
  lalu cek halaman ini — harusnya muncul baris baru dengan detail field apa yang berubah, dari nilai
  apa ke nilai apa, dan siapa yang melakukannya.
- Coba juga buat petugas baru dari akun admin-puskesmas, lalu cek dari sisi dinkes — walaupun
  petugas dibuat oleh admin-puskesmas (bukan dinkes), aktivitasnya tetap tercatat dan bisa dilihat dinkes.

## Backup otomatis terenkripsi

File tambahan (di folder `config-tambahan/`, **bukan** langsung ditimpa otomatis — baca cara pasang di bawah):
- `config-tambahan/backup.php` — contoh konfigurasi `spatie/laravel-backup` dengan enkripsi aktif
- `config-tambahan/console-schedule-snippet.php` — kode jadwal otomatis untuk ditempel ke `routes/console.php`

### Package yang wajib di-install dulu

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

### Cara pasang (perlu penyesuaian manual, tidak bisa asal timpa)

1. Setelah publish, akan muncul `config/backup.php` bawaan package. **Bandingkan** dengan
   `config-tambahan/backup.php` di paket ini, lalu sesuaikan bagian `source`, `destination`,
   `password`, `encryption`, dan `notifications` sesuai contoh — jangan asal timpa seluruh file
   kalau kamu sudah custom bagian lain.
2. Tambah ke `.env`:
   ```env
   BACKUP_ARCHIVE_PASSWORD=password-panjang-dan-acak-disini
   BACKUP_NOTIFICATION_EMAIL=dinkes@example.test
   ```
   **Password ini beda dari password login manapun** — kalau hilang, backup terenkripsi tidak bisa
   dibuka sama sekali (termasuk oleh kamu sendiri). Simpan di password manager, bukan cuma di `.env`.
3. Konfigurasi disk tujuan backup di `config/filesystems.php`, tambahkan disk baru misalnya:
   ```php
   'backup-remote' => [
       'driver' => 's3', // atau 'sftp', tergantung storage yang kamu punya
       // ... kredensial sesuai provider (AWS S3, DigitalOcean Spaces, dll)
   ],
   ```
   **Penting:** disk tujuan backup **jangan** disk yang sama dengan server aplikasi utama —
   kalau server utama kena masalah (hardware rusak, ransomware, dst), backup yang nyimpan di
   server yang sama ikut hilang. Idealnya simpan di cloud storage terpisah.
4. Tempel isi `config-tambahan/console-schedule-snippet.php` ke `routes/console.php` (project kamu),
   di bagian bawah, tanpa menghapus isi yang sudah ada di sana.
5. **Wajib** aktifkan Laravel Scheduler di server (bukan cuma nulis kode-nya) — tambahkan 1 baris ini
   di crontab server (`crontab -e` di Linux, atau Task Scheduler kalau di Windows Laragon):
   ```
   * * * * * cd /path-ke-project && php artisan schedule:run >> /dev/null 2>&1
   ```
   Tanpa baris ini, jadwal backup **tidak akan pernah jalan otomatis**, meskipun kodenya sudah benar.

### Cara tes manual (tanpa nunggu jadwal otomatis)

```bash
php artisan backup:run
php artisan backup:list
```
Cek juga email masuk ke `BACKUP_NOTIFICATION_EMAIL` (kalau `MAIL_MAILER=log`, cek `storage/logs/laravel.log`).

## Pertanyaan survei kustom per unit + unit layanan (poli)

Perubahan besar: setiap Puskesmas/RSU sekarang bisa atur pertanyaan surveinya sendiri
(boleh lebih dari 9, boleh beda kata-kata), tapi nilai SKM resmi tetap dihitung dari 9 unsur
baku Permenpan RB 14/2017 — bukan dari jumlah pertanyaan.

### Konsep intinya

- Tabel baru `pertanyaan_survei`: milik satu puskesmas, opsional dikaitkan ke salah satu
  unsur baku (`unsur_pelayanan_id`). Dikaitkan → ikut rumus SKM resmi. Kosong (`null`) →
  "pertanyaan tambahan", tetap tersimpan & ditampilkan di laporan, tapi di luar rumus.
- Tabel baru `unit_layanan`: daftar poli/layanan milik tiap puskesmas (mis. "Poli Umum",
  "UGD"), muncul sebagai dropdown di form survei publik — menggantikan input teks bebas yang lama.
- **Saat dinkes membuat Puskesmas baru**, sistem otomatis bikin 9 `pertanyaan_survei` dari
  master `unsur_pelayanan` (teks awalnya sama persis dengan master, admin-puskesmas tinggal
  edit kata-katanya atau tambah pertanyaan baru lewat menu **Pertanyaan Survei**).
- `SkmCalculatorService` sekarang menghitung ulang per unsur berdasarkan pertanyaan mana
  saja milik unit tsb yang dikaitkan ke unsur itu (bisa juga lebih dari 1 pertanyaan per
  unsur, otomatis dirata-rata). Kalau ada unsur yang belum dikaitkan ke pertanyaan aktif
  apa pun, sistem kasih peringatan di halaman **Pertanyaan Survei** (puskesmas) dan halaman
  **Laporan** (dinkes & puskesmas) — supaya kelihatan kalau ada unsur yang "bolong".

### Menu baru di panel admin-puskesmas

- **Pertanyaan Survei**: CRUD pertanyaan, tiap pertanyaan bisa dikaitkan ke salah satu U1-U9
  atau dibiarkan sebagai pertanyaan tambahan. Tidak bisa dihapus kalau sudah ada jawaban
  responden (nonaktifkan saja).
- **Unit Layanan**: CRUD poli/layanan, otomatis muncul di dropdown form survei publik unit tsb.

### Migration baru (urutan penting, jalankan berurutan)

1. `create_pertanyaan_survei_table`
2. `create_unit_layanan_table`
3. `update_survei_jawaban_detail_table` — mengganti kolom `unsur_pelayanan_id` jadi `pertanyaan_survei_id`
4. `update_survei_jawaban_table` — mengganti kolom teks `unit_layanan` jadi FK `unit_layanan_id`

**Penting:** kalau kamu sudah pernah isi data survei pakai skema lama (skema `unsur_pelayanan_id`
langsung), migration #3 akan **menghapus kolom lama beserta datanya**. Karena sejauh ini yang
ada baru data hasil testing, cara paling aman adalah:
```bash
php artisan migrate:fresh --seed
```
Ini akan reset total database + isi ulang data demo (termasuk 9 pertanyaan baseline &
3 contoh unit layanan untuk "Puskesmas Contoh"). **Jangan** pakai `migrate:fresh` kalau
sudah ada data survei sungguhan yang tidak boleh hilang — hubungi saya dulu kalau sudah
di tahap itu, supaya migration-nya dibuat lebih hati-hati (migrasi data lama, bukan drop kolom).

### Cara pasang

1. Copy semua file ke lokasi yang sama.
2. Jalankan `php artisan migrate:fresh --seed` (lihat catatan di atas).
3. Login sebagai admin-puskesmas → cek menu **Pertanyaan Survei**, harusnya sudah ada 9
   baris otomatis dari seeder.
4. Tambah 1-2 pertanyaan baru tanpa kaitan unsur (pertanyaan tambahan), coba juga tambah
   unit layanan baru.
5. Buka link survei publik unit itu → pastikan semua pertanyaan (9 baku + tambahan) muncul,
   dan dropdown unit layanan terisi.
6. Isi survei, submit, lalu cek **Laporan** — nilai SKM resmi harusnya cuma dari 9 unsur baku,
   dan pertanyaan tambahan muncul terpisah di tabel bawahnya dengan rata-rata sendiri.

## Update: pertanyaan bisa kustom penuh (tipe input, gaya tampilan, label skala) + mulai kosong

Perubahan lanjutan dari fitur "pertanyaan survei kustom" sebelumnya:

- **Unit baru mulai kosong.** Waktu dinkes bikin Puskesmas/RSU baru, sistem **tidak lagi**
  otomatis mengisi 9 pertanyaan baseline. Admin-puskesmas menyusun kuesionernya sendiri
  dari nol, termasuk memutuskan sendiri mana yang mau dikaitkan ke unsur U1-U9.
- **Tipe jawaban per pertanyaan**: `skala` (dinilai 1-4) atau `teks` (masukan bebas/opsional,
  tidak bisa dikaitkan ke unsur karena bukan angka).
- **Gaya tampilan** (khusus tipe skala): `radio` (tombol pilihan) atau `dropdown`.
- **Label skala kustom per pertanyaan** — admin bisa isi manual atau pakai salah satu preset
  cepat: Mutu umum (Buruk-Sangat Baik), Frekuensi (Tidak Pernah-Selalu), Ketersediaan sarana,
  Keramahan petugas, Kompetensi petugas — atau kosongkan semua supaya tampil angka 1-4 biasa.
  Preset ada di `app/Support/PresetLabelSkala.php`, gampang ditambah/diubah kalau perlu preset lain.

### Migration tambahan (jalankan setelah migration sebelumnya)

- `add_tipe_input_to_pertanyaan_survei_table` — kolom `tipe_input`, `gaya_tampilan`, 4 kolom label
- `add_jawaban_teks_to_survei_jawaban_detail_table` — kolom `nilai` jadi nullable, tambah kolom `jawaban_teks`

**Catatan teknis:** migration kedua memakai `->change()` untuk mengubah kolom `nilai` jadi nullable.
Kalau muncul error terkait ini saat `php artisan migrate`, jalankan dulu:
```bash
composer require doctrine/dbal
```
lalu migrate ulang.

### Cara pasang

```bash
php artisan migrate:fresh --seed
```
(sama seperti sebelumnya — karena masih tahap development, reset total lebih aman daripada
migration bertahap. Kabari saya kalau sudah ada data survei sungguhan yang tidak boleh hilang.)

### Yang bisa dites

1. Login admin-puskesmas → menu **Pertanyaan Survei** → data demo dari seeder sekarang punya
   label kustom per unsur (bukan cuma angka 1-4), coba buka salah satu buat lihat.
2. Coba **Tambah pertanyaan** baru: pilih tipe "Teks bebas" → field unsur & label otomatis hilang.
   Ganti balik ke "Skala" → coba pilih salah satu preset label, lihat 4 kotak label terisi otomatis,
   lalu edit manual kalau mau beda dari preset.
3. Buka link survei publik unit ini → pertanyaan skala tampil sesuai gaya (radio/dropdown) dengan
   label kustomnya, pertanyaan teks tampil sebagai kotak isian bebas (boleh dikosongkan).
4. Coba juga bikin Puskesmas baru dari sisi dinkes → login sebagai admin unit baru itu →
   pastikan menu **Pertanyaan Survei** memang kosong total, sesuai yang diminta.

## Update: field data diri responden (nama, no HP, dropdown usia/pendidikan/pekerjaan)

Perubahan di form survei publik:

| Field | Sebelum | Sesudah |
|---|---|---|
| Nama | tidak ada | **Baru**, wajib diisi |
| No. WA/HP | tidak ada | **Baru**, wajib diisi |
| Unit layanan (poli) | dropdown, opsional | dropdown (sama), **sekarang wajib** |
| Jenis kelamin | dropdown, opsional | tidak berubah (masih opsional) |
| Rentang usia | dropdown generik | diganti 8 kategori resmi Kemenkes, wajib |
| Pendidikan terakhir | input teks bebas | diganti dropdown (SD/SMP/SMA/D3/S1/S2/S3), wajib |
| Pekerjaan | input teks bebas | diganti dropdown (PNS/Wirausaha/dst), wajib |

Opsi dropdown-nya saya taruh di satu tempat: `app/Support/OpsiDataDiri.php` — kalau nanti mau
tambah/ubah pilihan, cukup edit array di situ, tidak perlu ubah controller atau view.

**Catatan privasi:** dengan nama & no HP sekarang wajib diisi, form ini bukan lagi anonim
sepenuhnya seperti desain awal. Ini konsekuensi wajar dari kebutuhan kamu, cuma perlu diingat
untuk bagian kepatuhan UU PDP yang pernah kita bahas — sebaiknya ada keterangan singkat di
form soal data ini dipakai untuk apa (misal: tindak lanjut keluhan), dan dijaga aksesnya
cuma oleh dinkes/admin unit terkait (yang sudah otomatis kejamin lewat scope per unit).

### Migration tambahan

`add_nama_no_hp_to_survei_jawaban_table` — tambah kolom `nama`, `no_hp` di tabel `survei_jawaban`.

### Cara pasang

```bash
php artisan migrate:fresh --seed
```

### Yang bisa dites

Buka link survei publik mana pun → pastikan field Nama & No. WA/HP muncul dan wajib diisi,
dropdown usia sudah 8 kategori Kemenkes, pendidikan & pekerjaan sudah jadi dropdown (bukan teks
bebas lagi), dan submit tanpa isi salah satu field wajib akan ditolak validasi.

## Dinkes juga punya SKM sendiri (kuesioner, laporan, link & QR)

Alih-alih bikin modul baru dari nol, Dinas Kesehatan sekarang diperlakukan sebagai **"unit" juga**
di tabel `puskesmas` (jenis baru: `dinkes`) — supaya otomatis dapat semua fitur yang sudah ada:
pertanyaan survei kustom, unit layanan, laporan (+ export PDF/Excel), link survei publik, dan QR code.
Tidak ada controller/view baru yang ditulis khusus untuk ini — murni reuse.

### Cara kerjanya

- User dengan role `dinkes` sekarang **juga** diberi role `admin-puskesmas` (dobel role), dan
  `puskesmas_id`-nya dikaitkan ke 1 baris khusus di tabel `puskesmas` bernama "Dinas Kesehatan"
  (`jenis = 'dinkes'`).
- Karena itu, halaman-halaman yang sebelumnya cuma bisa diakses admin-puskesmas biasa
  (`/puskesmas/dashboard`, `/puskesmas/pertanyaan`, `/puskesmas/unit-layanan`, `/puskesmas/laporan`)
  otomatis bisa diakses user dinkes juga — datanya otomatis punya Dinas Kesehatan sendiri, terpisah
  dari data milik puskesmas/RSU lain (karena tetap di-scope lewat `puskesmas_id`).
- Unit "Dinas Kesehatan" ini **sengaja disembunyikan** dari daftar Puskesmas/RSU yang dikelola
  di panel dinkes (`/dinkes/puskesmas`), dan **tidak ikut campur** ke rekap gabungan semua unit
  di `/dinkes/laporan` — supaya dinkes tidak bisa nonaktifkan diri sendiri secara tidak sengaja,
  dan SKM Dinkes dilihat terpisah, bukan tercampur ke rata-rata 29 puskesmas.
- Navbar dinkes sekarang ada menu **"SKM Dinkes Sendiri"** → masuk ke panel yang persis sama
  seperti panel admin-puskesmas, tapi datanya milik Dinas Kesehatan. Dari situ ada juga tombol
  balik **"Panel Pengawasan Dinkes"** untuk kembali ke mode pengawasan semua unit.

### Migration baru

`add_dinkes_as_puskesmas_jenis` — menambah nilai `dinkes` ke enum `jenis`, membuat baris
"Dinas Kesehatan" di tabel `puskesmas`, dan (untuk instalasi yang sudah ada datanya, bukan baru)
otomatis mengaitkan user ber-role `dinkes` yang sudah ada ke unit ini + kasih role tambahan
`admin-puskesmas`.

### Cara pasang

```bash
php artisan migrate:fresh --seed
```
Data demo dari `DemoDataSeeder` sekarang juga mengisi kuesioner contoh (9 unsur baku + 1 pertanyaan
teks) dan 3 unit layanan contoh ("Loket Pengaduan", dll) untuk Dinas Kesehatan.

### Yang bisa dites

1. Login sebagai `dinkes@example.test` → klik **SKM Dinkes Sendiri** di navbar.
2. Cek menu **Pertanyaan Survei** & **Unit Layanan** — sudah ada data contoh, bisa diedit/ditambah
   persis seperti admin-puskesmas biasa.
3. Cek **Dashboard** — ada QR code & link survei publik milik Dinas Kesehatan sendiri, coba scan/buka.
4. Isi survei publik itu, submit, cek **Laporan** — nilai SKM-nya cuma dari data Dinas Kesehatan,
   terpisah dari 29 puskesmas lain.
5. Balik ke **Panel Pengawasan Dinkes**, cek `/dinkes/puskesmas` dan `/dinkes/laporan` — pastikan
   "Dinas Kesehatan" **tidak muncul** di kedua daftar itu.

## Form Request class terpisah (validasi tidak lagi inline)

Semua `$request->validate([...])` inline di controller sudah dipindah ke class `FormRequest`
tersendiri di `app/Http/Requests/`:

| Controller | Form Request |
|---|---|
| `Dinkes\PuskesmasController` | `StorePuskesmasRequest`, `UpdatePuskesmasRequest` |
| `Dinkes\UnsurPelayananController` | `StoreUnsurPelayananRequest`, `UpdateUnsurPelayananRequest` |
| `Dinkes\PeriodeSurveiController` | `PeriodeSurveiRequest` (dipakai bersama store & update) |
| `Puskesmas\PetugasController` | `StorePetugasRequest`, `UpdatePetugasRequest` |
| `Puskesmas\PertanyaanSurveiController` | `PertanyaanSurveiRequest` |
| `Puskesmas\UnitLayananController` | `UnitLayananRequest` |
| `SurveiPublikController` | `StoreSurveiJawabanRequest` (rules dinamis sesuai kuesioner unit) |

Beberapa hal yang ikut dirapikan sekalian:

- **Proteksi lintas-unit dipindah ke `authorize()`.** Controller yang tadinya punya method privat
  `pastikanSatuUnit()` dipanggil manual di setiap action, sekarang pengecekannya juga hidup di
  `authorize()` milik Form Request-nya untuk `store()`/`update()` — jadi kalau ada yang lupa
  memanggilnya di masa depan, validasi tetap otomatis jalan. Method `pastikanSatuUnit()` masih
  dipertahankan untuk `edit()`/`destroy()` yang memang tidak punya body request untuk divalidasi.
- **`StoreSurveiJawabanRequest` yang paling rumit** — karena aturan validasinya dinamis (jumlah
  & tipe pertanyaan beda-beda per unit), 3 pengecekan prasyarat (unit aktif, periode aktif ada,
  kuesioner tidak kosong) juga dipindah ke `authorize()`, dilempar sebagai exception supaya
  responnya tetap sama seperti sebelumnya (404 kalau unit nonaktif, redirect + pesan kalau
  periode/kuesioner belum siap) — bukan pesan error 403 generik bawaan Form Request.

Tidak ada perubahan perilaku dari sisi pengguna — cuma perapian struktur kode.

## Halaman error 429 kustom (terlalu banyak percobaan)

File baru: `resources/views/errors/429.blade.php`.

Laravel otomatis pakai file ini setiap ada response HTTP 429 di seluruh aplikasi — tidak perlu
ubah route, controller, atau daftarkan apa pun secara manual, cukup taruh file-nya di lokasi ini
dan Laravel akan menemukannya sendiri. Saat ini satu-satunya tempat yang bisa memicu 429 adalah
rate limit di form submit survei publik (`throttle:15,10` yang sudah dipasang sebelumnya).

Halaman ini pakai layout publik yang sama dengan form survei (`layouts.publik`) supaya konsisten,
dan otomatis menghitung "coba lagi dalam X menit" dari header `Retry-After` yang dikirim Laravel,
bukan angka yang di-hardcode.

### Cara pasang

Cukup copy file `resources/views/errors/429.blade.php` ke lokasi yang sama. Tidak ada migration,
tidak ada perubahan controller/route.

### Cara tes

Submit form survei publik lebih dari 15 kali dalam 10 menit dari device/browser yang sama —
percobaan ke-16 dst harusnya menampilkan halaman ini (bukan lagi halaman 429 putih polos bawaan
Laravel), lengkap dengan estimasi waktu tunggu dan tombol "Kembali ke Form".

## Daftar isi masukan teks bebas di laporan

Sebelumnya tabel "Pertanyaan Tambahan" cuma nampilin jumlah jawaban + rata-rata (khusus tipe
skala). Sekarang pertanyaan tipe **teks** dapat tombol "Lihat jawaban" yang membuka halaman
daftar isi masukan satu-satu (dengan paginasi), lengkap dengan nama & no HP pengisi — supaya
kalau ada keluhan/saran yang perlu ditindaklanjuti, dinkes/admin-puskesmas bisa langsung tahu
siapa yang menulisnya.

### File baru

- `Dinkes\LaporanController@jawabanTeks` + `resources/views/dinkes/laporan/jawaban-teks.blade.php`
- `Puskesmas\LaporanController@jawabanTeks` + `resources/views/puskesmas/laporan/jawaban-teks.blade.php`
- Route baru: `dinkes.laporan.jawaban-teks`, `puskesmas.laporan.jawaban-teks`

`SkmCalculatorService` juga sedikit diubah — tiap entri `pertanyaan_tambahan` sekarang menyertakan
`id` pertanyaan, dipakai untuk bikin link "Lihat jawaban" tsb.

### Cara pasang

Tidak ada migration baru. Copy semua file di atas, pastikan `LaporanController` (dinkes & puskesmas)
dan `SkmCalculatorService` yang lama ditimpa dengan versi ini.

### Cara tes

1. Isi survei publik yang punya pertanyaan tambahan tipe teks (dari data demo: "Ada saran atau
   masukan lain untuk kami?"), isi kotak teksnya, submit.
2. Buka **Laporan** (dinkes atau puskesmas) → tabel "Pertanyaan Tambahan" → klik **Lihat jawaban**
   di baris pertanyaan teks tsb.
3. Pastikan muncul isi masukannya, nama & no HP pengisi, dan tanggal submit — kalau lebih dari
   20 masukan, coba juga paginasinya.
4. Dari sisi dinkes, coba buka halaman ini untuk salah satu puskesmas — pastikan cuma nampilin
   masukan milik puskesmas itu, bukan campur semua unit.

## Redesign landing page (font Poppins, warna ungu, ilustrasi)

File yang diganti total: `resources/views/welcome.blade.php` — halaman default Laravel
(yang isinya link dokumentasi Laravel/Laracast) sudah dihapus, diganti landing page profesional.

### Yang dipakai

- **Font**: Poppins (dari Google Fonts CDN), dipakai konsisten di seluruh halaman — bobot 300-800
  untuk variasi hierarki (heading tebal 800, body 400, label 600).
- **Warna primary**: ungu, mirip nuansa Google Forms — `#6D28D9` sebagai warna utama, dengan
  beberapa turunan (`#7C3AED` untuk hover, `#2E1065` untuk footer/gradient gelap, `#EDE9FE`
  untuk background lembut). Semua didefinisikan sebagai CSS custom property (`:root { --purple-700: ... }`)
  di bagian atas file, jadi gampang di-adjust nanti kalau mau ganti sedikit shade-nya.
- **Ilustrasi**: bukan file dari storyset.com (situsnya butuh interaksi JS untuk pilih & unduh,
  di luar akses jaringan sandbox saya) — saya buat **ilustrasi orisinal** bergaya flat illustration
  serupa (orang + kartu kuesioner + checklist + rating bintang 1-4), langsung sebagai inline SVG
  di dalam file, jadi tidak butuh request ke server luar sama sekali (lebih cepat & tidak rawan putus).
- **Layout hero**: teks di kiri, ilustrasi di kanan (sesuai diminta), otomatis stack jadi 1 kolom
  di layar sempit (mobile).

### Struktur halaman

Navbar → Hero (headline + CTA) → strip kepercayaan → 4 kartu fitur → 3 langkah cara kerja →
CTA banner → footer. Tombol "Masuk ke Sistem" mengarah ke `route('login')` (halaman login Breeze
yang sudah ada, tidak diubah).

### Cara pasang

Copy `resources/views/welcome.blade.php` ke lokasi yang sama (timpa file lama). Tidak ada
migration, tidak ada perubahan controller/route — halaman ini dipakai oleh route `/` yang
sudah ada bawaan Laravel.

### Kalau nanti mau ganti ilustrasi jadi asli dari storyset.com

1. Buka storyset.com, cari ilustrasi bertema survei/kuesioner/checklist, pilih warna ungu
   biar senada, lalu download sebagai SVG.
2. Simpan file itu di `public/images/hero-illustration.svg`.
3. Di `welcome.blade.php`, cari blok `<svg viewBox="0 0 520 480" ...> ... </svg>` di bagian
   `.hero-illustration`, ganti seluruhnya jadi:
   ```html
   <img src="{{ asset('images/hero-illustration.svg') }}" alt="Ilustrasi survei kepuasan">
   ```

### Catatan

Ini baru landing page-nya saja sesuai permintaan awal ("mulai dari halaman landing dulu").
Halaman lain (login, dashboard dinkes/puskesmas, form survei publik) masih pakai tampilan
Bootstrap yang lama — belum ikut dirombak ke tema Poppins + ungu ini. Kalau nanti mau lanjut
ke halaman-halaman itu, warna & fontnya bisa ditarik dari variabel `:root` di file ini supaya
konsisten satu tema di seluruh sistem.

## Belum termasuk di paket ini (langkah selanjutnya)

- Redesign halaman-halaman lain (login, dashboard, form survei) ke tema Poppins + ungu yang sama
