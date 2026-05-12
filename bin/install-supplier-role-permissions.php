<?php

/**
 * Grant role "supplier" access to all admin endpoints used by the supplier panel.
 * Does NOT load app config. Uses only env vars for DB.
 *
 * Run from backend:
 *   MYSQL_DB_HOST=... MYSQL_DB_USER=... MYSQL_DB_PASSWORD=... MYSQL_DB_NAME=knowledge php module/Content/bin/install-supplier-role-permissions.php
 *
 * Covers:
 *   - admin/content/* (supplier, material, material-offer, industry): item-get, list, add, edit, delete, update
 *   - admin/support/item/* (tickets, requests): item-get, list, edit, update
 *   - admin/user/profile/* (profile list/add/edit/password used in request dialogs)
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

// key, resource, section, module, role
$rows = [
    // Content: supplier, material, material-offer, industry (admin/content/*)
    ['supplier-admin-content-item-get', 'admin-content-item-get', 'admin', 'content', 'supplier'],
    ['supplier-admin-content-item-list', 'admin-content-item-list', 'admin', 'content', 'supplier'],
    ['supplier-admin-content-item-add', 'admin-content-item-add', 'admin', 'content', 'supplier'],
    ['supplier-admin-content-item-edit', 'admin-content-item-edit', 'admin', 'content', 'supplier'],
    ['supplier-admin-content-item-delete', 'admin-content-item-delete', 'admin', 'content', 'supplier'],
    ['supplier-admin-content-item-update', 'admin-content-item-update', 'admin', 'content', 'supplier'],
    // Support: tickets, requests (admin/support/item/*)
    ['supplier-admin-support-item-get', 'admin-support-item-get', 'admin', 'support', 'supplier'],
    ['supplier-admin-support-item-list', 'admin-support-item-list', 'admin', 'support', 'supplier'],
    ['supplier-admin-support-item-edit', 'admin-support-item-edit', 'admin', 'support', 'supplier'],
    ['supplier-admin-support-item-update', 'admin-support-item-update', 'admin', 'support', 'supplier'],
    // User: profile list/add/edit/password (admin/user/profile/*)
    ['supplier-user-profile-list', 'user-profile-list', 'admin', 'user', 'supplier'],
    ['supplier-user-profile-add', 'user-profile-add', 'admin', 'user', 'supplier'],
    ['supplier-user-profile-edit', 'user-profile-edit', 'admin', 'user', 'supplier'],
    ['supplier-user-profile-password', 'user-profile-password', 'admin', 'user', 'supplier'],
];

$dsn = 'mysql:dbname=' . $dbName . ';host=' . $dbHost . ';charset=utf8';
$options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO `permission_role` (`key`, `resource`, `section`, `module`, `role`) VALUES (?, ?, ?, ?, ?)"
    );
    foreach ($rows as $row) {
        $stmt->execute($row);
        echo "Permission role: {$row[1]} ({$row[3]}) -> {$row[4]}\n";
    }
    echo "OK. Supplier panel permissions installed.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
