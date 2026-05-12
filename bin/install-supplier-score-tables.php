<?php

/**
 * Create content_score_type and content_supplier_score tables + seed score types.
 * Run from backend: php module/Content/bin/install-supplier-score-tables.php
 * With DB env: MYSQL_DB_HOST=... MYSQL_DB_USER=... MYSQL_DB_PASSWORD=... MYSQL_DB_NAME=knowledge
 */

declare(strict_types=1);

$dbName = getenv('MYSQL_DB_NAME') ?: 'knowledge';
$dbHost = getenv('MYSQL_DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('MYSQL_DB_USER') ?: '';
$dbPass = getenv('MYSQL_DB_PASSWORD') !== false ? getenv('MYSQL_DB_PASSWORD') : '';

if ($dbUser === '') {
    fwrite(STDERR, "Set env: MYSQL_DB_HOST, MYSQL_DB_USER, MYSQL_DB_PASSWORD, MYSQL_DB_NAME\n");
    exit(1);
}

$dataDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';
$files = [
    $dataDir . DIRECTORY_SEPARATOR . 'content_score_type.sql',
    $dataDir . DIRECTORY_SEPARATOR . 'content_supplier_score.sql',
];

$dsn = 'mysql:dbname=' . $dbName . ';host=' . $dbHost . ';charset=utf8';
$options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    foreach ($files as $file) {
        if (!is_file($file)) {
            fwrite(STDERR, "File not found: $file\n");
            exit(1);
        }
        $sql = file_get_contents($file);
        $pdo->exec($sql);
        echo "OK: " . basename($file) . "\n";
    }
    echo "Done. Score types and supplier_score table ready.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
