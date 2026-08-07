<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetUserPassword extends Command
{
    /**
     * php artisan skm:set-password admin.puskesmas-baru@example.test
     * php artisan skm:set-password admin.puskesmas-baru@example.test password123
     */
    protected $signature = 'skm:set-password {email} {password=password}';

    protected $description = 'Set/reset password sebuah akun ke nilai tertentu (default: "password") — untuk kebutuhan testing/data dummy lokal, BUKAN untuk dipakai sembarangan di production.';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Tidak ada akun dengan email: {$email}");

            return self::FAILURE;
        }

        if (app()->environment('production')) {
            $lanjut = $this->confirm(
                "PERINGATAN: kamu sedang di environment PRODUCTION. Yakin mau set password {$user->email} secara manual lewat command ini?",
                false
            );

            if (! $lanjut) {
                $this->info('Dibatalkan.');

                return self::SUCCESS;
            }
        }

        $user->update(['password' => Hash::make($password)]);

        $this->info("Password untuk {$user->email} berhasil di-set ke: {$password}");

        return self::SUCCESS;
    }
}
