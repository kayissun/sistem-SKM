<?php
// Tempel blok ini ke routes/console.php DI PROJECT KAMU (jangan timpa seluruh file,
// cukup tambahkan bagian Schedule:: di bawah ini ke isi file yang sudah ada).
//
// routes/console.php di Laravel 11/12/13 dipakai juga untuk mendaftarkan jadwal (scheduler),
// menggantikan app/Console/Kernel.php di versi Laravel lama.

use Illuminate\Support\Facades\Schedule;

// Backup database + file setiap hari jam 01:30 dini hari (saat trafik paling sepi)
Schedule::command('backup:run')
    ->dailyAt('01:30')
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Backup harian gagal dijalankan.');
    });

// Bersihkan backup lama sesuai aturan retensi di config/backup.php (jam 03:00, setelah backup selesai)
Schedule::command('backup:clean')->dailyAt('03:00');

// Cek kesehatan backup (apakah masih ada backup yang cukup baru, dsb) tiap pagi jam 07:00
Schedule::command('backup:monitor')->dailyAt('07:00');
