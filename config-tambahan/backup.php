<?php
// Salin file ini ke config/backup.php DI PROJECT KAMU (menimpa file bawaan package
// yang otomatis muncul setelah `php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"`).
//
// Ini BUKAN file config lengkap dari awal — cuma bagian-bagian yang perlu disesuaikan
// dari default package. Kalau kamu sudah publish config aslinya, cukup ubah bagian yang
// disebutkan di README (jangan main timpa seluruh file kalau sudah ada kustomisasi lain).

return [
    'backup' => [
        'name' => env('APP_NAME', 'sistem-skm'),

        'source' => [
            'files' => [
                'include' => [
                    base_path(),
                ],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    storage_path(),
                ],
            ],

            // backup seluruh database yang dipakai aplikasi (nama koneksi default dari .env)
            'databases' => [
                'mysql',
            ],
        ],

        'database_dump_compressor' => \Spatie\DbDumper\Compressors\GzipCompressor::class,

        'destination' => [
            'filename_prefix' => '',
            'disks' => [
                'backup-remote', // lihat README: disk ini harus disetting terpisah dari storage lokal
            ],
        ],

        // ENKRIPSI: password diambil dari .env, JANGAN taruh password langsung di file ini.
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption' => 'default', // otomatis pakai AES-256 kalau tersedia di server

        'verify_backup' => true, // pastikan hasil zip valid & tidak rusak setiap selesai backup
    ],

    'notifications' => [
        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => [],
        ],

        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFICATION_EMAIL', 'dinkes@example.test'),
        ],
    ],

    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'sistem-skm'),
            'disks' => ['backup-remote'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 2, // maks 2 hari sejak backup terakhir
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 30,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 12,
            'keep_yearly_backups_for_years' => 2,
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],
];
