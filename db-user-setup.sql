-- Jalankan sebagai user MySQL yang punya hak admin (misal root), SATU KALI SAJA di server production.
-- Ganti 'nama_database', 'user_aplikasi', dan 'password_kuat_disini' sesuai kebutuhanmu.

-- 1. Buat database (skip kalau sudah ada)
CREATE DATABASE IF NOT EXISTS nama_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Buat user khusus aplikasi (hanya bisa diakses dari localhost, ganti '%' kalau app & DB beda server)
CREATE USER IF NOT EXISTS 'user_aplikasi'@'localhost' IDENTIFIED BY 'password_kuat_disini';

-- 3. Beri izin HANYA untuk operasi data biasa, TIDAK termasuk DROP/CREATE/GRANT/ALTER USER
GRANT SELECT, INSERT, UPDATE, DELETE ON nama_database.* TO 'user_aplikasi'@'localhost';

-- 4. Izin tambahan yang tetap dibutuhkan Laravel untuk migration (kalau migration dijalankan pakai user ini juga)
--    Kalau migration selalu dijalankan manual oleh developer/DBA pakai user admin terpisah, baris ini BOLEH dilewati
--    supaya user aplikasi produksi benar-benar tidak bisa mengubah struktur tabel.
-- GRANT CREATE, ALTER, INDEX, REFERENCES ON nama_database.* TO 'user_aplikasi'@'localhost';

FLUSH PRIVILEGES;

-- Cara pakai di .env production:
-- DB_DATABASE=nama_database
-- DB_USERNAME=user_aplikasi
-- DB_PASSWORD=password_kuat_disini
