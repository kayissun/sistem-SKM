# Checklist Keamanan Sebelum Production

Checklist ini khusus untuk persiapan deploy sistem SKM ke server production (bukan lagi di Laragon lokal).
Kerjakan urut dari atas — beberapa poin saling bergantung.

## 1. Konfigurasi `.env` production

```env
APP_ENV=production
APP_DEBUG=false          # WAJIB false. Kalau true, error nampilin stack trace + query DB ke publik
APP_URL=https://domain-asli-kamu.go.id

SESSION_SECURE_COOKIE=true   # cookie sesi cuma dikirim lewat HTTPS
SESSION_ENCRYPT=true         # enkripsi isi cookie sesi

MAIL_MAILER=smtp             # ganti dari 'log', pakai SMTP asli (lihat README bagian email)
```

**Cara cek cepat:** buka `/dinkes/dashboard` dari luar server pakai URL production. Kalau `APP_DEBUG`
masih `true` dan sengaja bikin error (misal akses route yang salah), harusnya TIDAK nampilin detail
teknis apa pun ke browser — cuma halaman error generik.

## 2. Wajib HTTPS

Pasang SSL certificate (gratis pakai [Let's Encrypt](https://letsencrypt.org) kalau hosting sendiri, atau
otomatis kalau pakai shared hosting/cloud panel). Tambahkan juga redirect otomatis HTTP → HTTPS di level
webserver (Apache/Nginx), bukan cuma di level Laravel.

Tambahkan ini di `app/Providers/AppServiceProvider.php`, dalam method `boot()`, supaya semua link yang
digenerate Laravel (termasuk link email reset password) otomatis pakai `https://`:

```php
use Illuminate\Support\Facades\URL;

public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

## 3. Document root & file permission

- Pastikan document root webserver mengarah ke folder **`public/`**, bukan root project. Kalau salah
  arah, orang bisa buka `.env`, `composer.json`, bahkan folder `app/` langsung dari browser.
- Folder `storage/` dan `bootstrap/cache/` butuh permission tulis untuk web server (biasanya `775`,
  owner sesuai user webserver), tapi tidak boleh bisa diakses langsung lewat URL.
- Kunci `.env` supaya tidak bisa dibaca lewat browser (Apache: file `.htaccess` bawaan Laravel di
  folder `public/` sudah menghalangi ini secara default, tinggal dipastikan modul `mod_rewrite` aktif).

## 4. Database user dengan hak akses terbatas (least privilege)

**Jangan** pakai user `root` MySQL untuk koneksi aplikasi. Buat user khusus yang cuma bisa
baca/tulis data, tidak bisa `DROP DATABASE` atau bikin user lain. Lihat `db-user-setup.sql`
di paket ini untuk skrip SQL-nya — tinggal sesuaikan nama database & password lalu jalankan
di MySQL server production.

## 5. Rate limit login (tambahan, opsional)

Laravel Breeze secara default sudah membatasi percobaan login (maksimal 5x gagal per email+IP,
lalu di-lockout sementara) — ini sudah aktif otomatis, tidak perlu setup tambahan. Cukup diketahui saja.

## 6. Backup

Belum termasuk di checklist ini — akan digarap terpisah (pakai `spatie/laravel-backup`) di iterasi
berikutnya, supaya bisa dijadwalkan otomatis + terenkripsi + disimpan di storage terpisah dari server utama.

## 7. Audit trail

Juga digarap terpisah di iterasi berikutnya (pakai `spatie/laravel-activitylog`), supaya semua
perubahan data penting (unit, unsur pelayanan, periode, akun user) tercatat siapa-kapan-apa yang diubah.
