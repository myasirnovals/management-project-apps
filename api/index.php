<?php

// Pastikan direktori storage di /tmp tersedia untuk lingkungan serverless Vercel
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $tmpStorage = '/tmp/storage';
    $tmpDirs = [
        $tmpStorage . '/framework/views',
        $tmpStorage . '/framework/cache/data',
        $tmpStorage . '/framework/sessions',
        $tmpStorage . '/logs',
        $tmpStorage . '/app/public',
    ];

    foreach ($tmpDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // Salin atau inisialisasi basis data SQLite di /tmp jika koneksi adalah sqlite
    $dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
    if ($dbConnection === 'sqlite') {
        $targetDb = '/tmp/database.sqlite';
        if (!file_exists($targetDb) || filesize($targetDb) === 0) {
            $sourceDb = __DIR__ . '/../database/database.sqlite';
            if (file_exists($sourceDb) && filesize($sourceDb) > 0) {
                @copy($sourceDb, $targetDb);
            } else {
                @touch($targetDb);
            }
        }
    }
}

require __DIR__ . '/../public/index.php';
