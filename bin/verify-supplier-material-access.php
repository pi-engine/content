<?php

/**
 * Verify and fix supplier access to admin/content/material/list (and related content endpoints).
 * Run from backend: php module/Content/bin/verify-supplier-material-access.php
 * With DB env: MYSQL_DB_HOST=... MYSQL_DB_USER=... MYSQL_DB_PASSWORD=... MYSQL_DB_NAME=knowledge
 *
 * If you get 403 "You dont have access to this area ! 2" on POST admin/content/material/list,
 * run this script then retry (no need to re-login).
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

$dsn = 'mysql:dbname=' . $dbName . ';host=' . $dbHost . ';charset=utf8';
$options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (Throwable $e) {
    fwrite(STDERR, "DB connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

// Required for material/list: permission_page key = admin-content-item-list, permission_role (resource=admin-content-item-list, role=supplier)
$checkPage = $pdo->query("SELECT 1 FROM permission_page WHERE `key` = 'admin-content-item-list' LIMIT 1");
if ($checkPage && $checkPage->fetch()) {
    echo "OK permission_page exists: admin-content-item-list\n";
} else {
    echo "MISSING permission_page for admin-content-item-list. Run: php module/Content/bin/install-content-permissions.php\n";
}

$checkRole = $pdo->prepare("SELECT 1 FROM permission_role WHERE resource = 'admin-content-item-list' AND role = 'supplier' LIMIT 1");
$checkRole->execute();
if ($checkRole->fetch()) {
    echo "OK permission_role exists: supplier -> admin-content-item-list\n";
} else {
    echo "MISSING permission_role for supplier + admin-content-item-list. Inserting...\n";
    $rows = [
        ['supplier-admin-content-item-get', 'admin-content-item-get', 'admin', 'content', 'supplier'],
        ['supplier-admin-content-item-list', 'admin-content-item-list', 'admin', 'content', 'supplier'],
        ['supplier-admin-content-item-add', 'admin-content-item-add', 'admin', 'content', 'supplier'],
        ['supplier-admin-content-item-edit', 'admin-content-item-edit', 'admin', 'content', 'supplier'],
        ['supplier-admin-content-item-delete', 'admin-content-item-delete', 'admin', 'content', 'supplier'],
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO permission_role (`key`, resource, section, module, role) VALUES (?, ?, ?, ?, ?)");
    foreach ($rows as $row) {
        $stmt->execute($row);
        if ($stmt->rowCount() > 0) {
            echo "  Inserted: {$row[1]} -> supplier\n";
        }
    }
    echo "Done. Retry the request (no need to re-login).\n";
}

echo "Verification finished.\n";
