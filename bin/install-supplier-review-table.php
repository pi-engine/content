<?php

/**
 * Create the content_supplier_review table. Does NOT load app config (no global.php).
 * Run from backend: php module/Content/bin/install-supplier-review-table.php
 *
 * Option 1 - Env vars (e.g. in Docker):
 *   MYSQL_DB_HOST=localhost MYSQL_DB_USER=root MYSQL_DB_PASSWORD=xxx MYSQL_DB_NAME=knowledge php module/Content/bin/install-supplier-review-table.php
 *
 * Option 2 - Run the SQL manually in phpMyAdmin/MySQL:
 *   See module/Content/data/content_supplier_review.sql
 */

declare(strict_types=1);

$dbName = getenv('MYSQL_DB_NAME') ?: 'knowledge';
$dbHost = getenv('MYSQL_DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('MYSQL_DB_USER') ?: '';
$dbPass = getenv('MYSQL_DB_PASSWORD') !== false ? getenv('MYSQL_DB_PASSWORD') : '';

if ($dbUser === '') {
    fwrite(STDERR, "Set env: MYSQL_DB_HOST, MYSQL_DB_USER, MYSQL_DB_PASSWORD, MYSQL_DB_NAME\n");
    fwrite(STDERR, "Example: MYSQL_DB_USER=root MYSQL_DB_PASSWORD=pass MYSQL_DB_NAME=knowledge php " . basename(__FILE__) . "\n");
    exit(1);
}

$sqlFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'content_supplier_review.sql';
if (!is_file($sqlFile)) {
    fwrite(STDERR, "SQL file not found: {$sqlFile}\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
$sql = trim(preg_replace('/^--.*$/m', '', $sql));
if ($sql === '') {
    fwrite(STDERR, "No SQL to execute.\n");
    exit(1);
}

$dsn = 'mysql:dbname=' . $dbName . ';host=' . $dbHost . ';charset=utf8';
$options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    $pdo->exec($sql);
    echo "OK. Table content_supplier_review created or already exists.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
