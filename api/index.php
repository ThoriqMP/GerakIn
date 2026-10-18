<?php

// Ensure writable directories in /tmp for Vercel Serverless execution
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/views',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Ensure SQLite database file exists in /tmp if using sqlite
$dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';
if ($dbConnection === 'sqlite') {
    $dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
    putenv("DB_DATABASE={$dbPath}");
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_DATABASE'] = $dbPath;

    if (!file_exists($dbPath)) {
        @touch($dbPath);
    }
}

// Forward to public/index.php
require __DIR__ . '/../public/index.php';
