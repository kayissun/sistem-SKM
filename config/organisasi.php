<?php

return [
    // Ditampilkan di header "Format Publikasi IKM". Ganti lewat .env (NAMA_INSTANSI)
    // supaya tidak perlu ubah kode kalau suatu saat sistem ini dipakai daerah lain.
    'nama' => env('NAMA_INSTANSI', 'Dinas Kesehatan Daerah Kabupaten Purworejo'),
];
