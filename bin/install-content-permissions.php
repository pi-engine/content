<?php

/**
 * Insert missing Content module permission pages so routes like material get/list/add/edit work.
 * Fixes: "Role with identifier admin-content-item-get not found."
 * Run from backend: php module/Content/bin/install-content-permissions.php
 * With DB env: MYSQL_DB_HOST=... MYSQL_DB_USER=... MYSQL_DB_PASSWORD=... MYSQL_DB_NAME=knowledge
 */

declare(strict_types=1);

$backendDir = getcwd();
$autoload  = $backendDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Run from backend directory (composer install).\n");
    exit(1);
}
require $autoload;

$dbName = getenv('MYSQL_DB_NAME') ?: 'knowledge';
$dbHost = getenv('MYSQL_DB_HOST');
$dbUser = getenv('MYSQL_DB_USER');
$dbPass = getenv('MYSQL_DB_PASSWORD');
if ($dbHost !== false && $dbHost !== '' && $dbUser !== false) {
    $db = [
        'driver'         => 'Pdo_Mysql',
        'dsn'            => 'mysql:dbname=' . $dbName . ';host=' . $dbHost . ';charset=utf8',
        'username'       => $dbUser,
        'password'       => $dbPass !== false ? $dbPass : '',
        'driver_options' => [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_general_ci'],
    ];
} else {
    $config = require $backendDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . 'global.php';
    if (file_exists($backendDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'development.config.php')) {
        $config = \Laminas\Stdlib\ArrayUtils::merge($config, require $backendDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'development.config.php');
    }
    $db = $config['db'] ?? null;
}
if (!$db || empty($db['dsn'])) {
    fwrite(STDERR, "Set MYSQL_DB_HOST, MYSQL_DB_USER, MYSQL_DB_PASSWORD, MYSQL_DB_NAME or use app config.\n");
    exit(1);
}

$keys = [
    'admin-content-item-get',
    'admin-content-item-list',
    'admin-content-item-add',
    'admin-content-item-edit',
    'admin-content-item-delete',
    'admin-content-item-update',
    'admin-content-supplier-review-list',
    'admin-content-supplier-review-update-status',
];

try {
    $adapter = new Laminas\Db\Adapter\Adapter($db);
    $platform = $adapter->getPlatform();
    $q = function ($v) use ($platform) {
        return $platform->quoteValue($v);
    };

    foreach ($keys as $key) {
        $parts = explode('-', $key);
        $handler = array_pop($parts); // get, list, add, edit, delete, update, update-status
        $package = strpos($key, 'supplier-review') !== false ? 'supplier-review' : 'item';
        $resource = $key;
        $roleKey = 'admin-' . $key;

        $adapter->query(
            "INSERT IGNORE INTO `permission_resource` (`title`, `key`, `section`, `module`, `type`) VALUES (" . $q($key) . ", " . $q($key) . ", 'admin', 'content', 'system')",
            $adapter::QUERY_MODE_EXECUTE
        );

        $adapter->query(
            "INSERT IGNORE INTO `permission_page` (`title`, `key`, `resource`, `section`, `module`, `package`, `handler`, `cache_type`, `cache_ttl`, `cache_level`) VALUES (" . $q($key) . ", " . $q($key) . ", " . $q($resource) . ", 'admin', 'content', " . $q($package) . ", " . $q($handler) . ", 'page', 0, '')",
            $adapter::QUERY_MODE_EXECUTE
        );

        $adapter->query(
            "INSERT IGNORE INTO `permission_role` (`key`, `resource`, `section`, `module`, `role`) VALUES (" . $q($roleKey) . ", " . $q($resource) . ", 'admin', 'content', 'admin')",
            $adapter::QUERY_MODE_EXECUTE
        );
        // Grant same resources to role "supplier" for supplier-panel admin endpoints
        if (strpos($key, 'supplier-review') === false) {
            $supplierKey = 'supplier-' . $key;
            $adapter->query(
                "INSERT IGNORE INTO `permission_role` (`key`, `resource`, `section`, `module`, `role`) VALUES (" . $q($supplierKey) . ", " . $q($resource) . ", 'admin', 'content', 'supplier')",
                $adapter::QUERY_MODE_EXECUTE
            );
        }

        echo "Permission: {$key}\n";
    }

    echo "Done. Content admin permissions (get, list, add, edit) installed. Supplier role granted for item-*.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
