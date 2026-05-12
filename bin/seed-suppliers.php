<?php

/**
 * Seed 3 suppliers (سیمیا، فولاد مبارکه، پتروشیمی خلیج) using the same data model as admin panel:
 * - user_account + user_profile (supplier admin user)
 * - role_account: member + supplier (section api)
 * - content_item type=supplier with information (company_name, supplier_admin_user_id, ...)
 *
 * Run from backend directory:
 *   php module/Content/bin/seed-suppliers.php
 * With DB env set (recommended, avoids loading full config):
 *   MYSQL_DB_HOST=... MYSQL_DB_USER=... MYSQL_DB_PASSWORD=... MYSQL_DB_NAME=knowledge php module/Content/bin/seed-suppliers.php
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
    fwrite(STDERR, "Set MYSQL_DB_HOST, MYSQL_DB_USER, MYSQL_DB_PASSWORD (and optionally MYSQL_DB_NAME).\n");
    exit(1);
}

$companies = [
    ['company' => 'سیمیا', 'slug' => 'supplier-simia', 'admin_email' => 'supplier.simia@example.com', 'admin_first_name' => 'مدیر', 'admin_last_name' => 'سیمیا'],
    ['company' => 'فولاد مبارکه', 'slug' => 'supplier-foolad-mobareke', 'admin_email' => 'supplier.foolad@example.com', 'admin_first_name' => 'مدیر', 'admin_last_name' => 'فولاد مبارکه'],
    ['company' => 'پتروشیمی خلیج', 'slug' => 'supplier-petroshimi-khalij', 'admin_email' => 'supplier.petroshimi@example.com', 'admin_first_name' => 'مدیر', 'admin_last_name' => 'پتروشیمی خلیج'],
];

$defaultPassword = 'Supplier@123';
$credentialHash = password_hash($defaultPassword, PASSWORD_BCRYPT);
$time = time();
$status = 1;

try {
    $adapter = new Laminas\Db\Adapter\Adapter($db);
    $sql     = new Laminas\Db\Sql\Sql($adapter);

    $adapter->query(
        "INSERT IGNORE INTO `role_resource` (`name`, `title`, `status`, `section`) VALUES ('supplier', 'Supplier', 1, 'api')",
        $adapter::QUERY_MODE_EXECUTE
    );

    foreach ($companies as $c) {
        $email = $c['admin_email'];
        $name  = trim(($c['admin_first_name'] ?? '') . ' ' . ($c['admin_last_name'] ?? ''));

        $sel = $sql->select('user_account')->columns(['id'])->where(['email' => $email]);
        $res = $adapter->query($sql->buildSqlString($sel), $adapter::QUERY_MODE_EXECUTE);
        $row = $res->current();
        if ($row && !empty($row['id'])) {
            echo "Skip (user exists): {$c['company']} ({$email})\n";
            continue;
        }

        $insAccount = new Laminas\Db\Sql\Insert('user_account');
        $insAccount->values([
            'name'         => $name ?: $c['company'],
            'email'        => $email,
            'credential'   => $credentialHash,
            'status'       => $status,
            'time_created' => $time,
        ]);
        $stmt   = $sql->prepareStatementForSqlObject($insAccount);
        $result = $stmt->execute();
        $userId = (int) $result->getGeneratedValue();

        $insProfile = new Laminas\Db\Sql\Insert('user_profile');
        $insProfile->values([
            'user_id'     => $userId,
            'first_name'  => $c['admin_first_name'] ?? '',
            'last_name'   => $c['admin_last_name'] ?? '',
            'information' => json_encode([], JSON_UNESCAPED_UNICODE),
        ]);
        $sql->prepareStatementForSqlObject($insProfile)->execute();

        foreach (['member', 'supplier'] as $role) {
            $insRole = new Laminas\Db\Sql\Insert('role_account');
            $insRole->values(['user_id' => $userId, 'role' => $role, 'section' => 'api']);
            $sql->prepareStatementForSqlObject($insRole)->execute();
        }

        $information = [
            'company_name'             => $c['company'],
            'admin_email'              => $email,
            'admin_first_name'         => $c['admin_first_name'] ?? '',
            'admin_last_name'          => $c['admin_last_name'] ?? '',
            'supplier_admin_user_id'   => $userId,
        ];

        $insItem = new Laminas\Db\Sql\Insert('content_item');
        $insItem->values([
            'type'        => 'supplier',
            'slug'        => $c['slug'],
            'title'       => $c['company'],
            'status'      => 1,
            'user_id'     => $userId,
            'time_create' => $time,
            'time_update' => $time,
            'time_delete' => 0,
            'parent_id'   => 0,
            'priority'    => 0,
            'information' => json_encode($information, JSON_UNESCAPED_UNICODE),
        ]);
        $itemResult = $sql->prepareStatementForSqlObject($insItem)->execute();
        $itemId     = (int) $itemResult->getGeneratedValue();

        echo "OK: {$c['company']} (supplier id={$itemId}, slug={$c['slug']}, user id={$userId}, email={$email})\n";
    }

    echo "Done. Supplier users can login with the emails above and password: {$defaultPassword}\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
