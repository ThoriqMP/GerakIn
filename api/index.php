<?php

// Forward HTTPS headers from Vercel proxy
if ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] === '443')) {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

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

// Database configuration for Vercel Serverless
$databaseUrl = getenv('DATABASE_URL') ?: (getenv('POSTGRES_URL') ?: getenv('DB_URL'));
$dbConnection = getenv('DB_CONNECTION');

// Check for unpopulated template placeholders like [YOUR-PASSWORD]
if ($databaseUrl && (
    str_contains($databaseUrl, '[YOUR-PASSWORD]') ||
    str_contains($databaseUrl, '[YOUR_PASSWORD]') ||
    str_contains($databaseUrl, '[PASSWORD]') ||
    str_contains($databaseUrl, '%5BYOUR-PASSWORD%5D')
)) {
    error_log('GerakIn: DATABASE_URL still contains placeholder password [YOUR-PASSWORD]. Falling back to SQLite.');
    $databaseUrl = null;
    putenv('DATABASE_URL=');
    $_ENV['DATABASE_URL'] = '';
    $_SERVER['DATABASE_URL'] = '';
}

if ($databaseUrl && empty($dbConnection)) {
    if (str_starts_with($databaseUrl, 'postgres://') || str_starts_with($databaseUrl, 'postgresql://')) {
        $dbConnection = 'pgsql';
        putenv('DB_CONNECTION=pgsql');
        $_ENV['DB_CONNECTION'] = 'pgsql';
        $_SERVER['DB_CONNECTION'] = 'pgsql';
    } elseif (str_starts_with($databaseUrl, 'mysql://')) {
        $dbConnection = 'mysql';
        putenv('DB_CONNECTION=mysql');
        $_ENV['DB_CONNECTION'] = 'mysql';
        $_SERVER['DB_CONNECTION'] = 'mysql';
    }
} elseif (empty($dbConnection) && getenv('DB_HOST')) {
    $port = (int) (getenv('DB_PORT') ?: 5432);
    $dbConnection = ($port === 3306) ? 'mysql' : 'pgsql';
    putenv("DB_CONNECTION={$dbConnection}");
    $_ENV['DB_CONNECTION'] = $dbConnection;
    $_SERVER['DB_CONNECTION'] = $dbConnection;
}

// Ensure SQLite database file exists in /tmp if using sqlite fallback
if (($dbConnection ?: 'sqlite') === 'sqlite' && empty($databaseUrl)) {
    $dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
    putenv("DB_DATABASE={$dbPath}");
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_DATABASE'] = $dbPath;

    if (!file_exists($dbPath)) {
        $sourceDb = __DIR__ . '/../database/database.sqlite';
        if (file_exists($sourceDb) && filesize($sourceDb) > 0) {
            @copy($sourceDb, $dbPath);
        } else {
            @touch($dbPath);
        }
    }
}

// Forward to public/index.php
require __DIR__ . '/../public/index.php';
