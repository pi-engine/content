<?php

/**
 * Seed content_item with industry and sub-industry from user/data/industries.json.
 * Uses type='meta-industry', parent_id=0 for industries, parent_id=industry_id for sub-industries.
 * Run from backend: php module/Content/bin/seed-industries.php
 * Optional: INDUSTRIES_JSON=/path/to/industries.json (default: ../user/data/industries.json from backend)
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

$jsonPath = getenv('INDUSTRIES_JSON') ?: $backendDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'industries.json';
if (!is_file($jsonPath)) {
    fwrite(STDERR, "Industries JSON not found: {$jsonPath}\n");
    exit(1);
}

$json = file_get_contents($jsonPath);
$industries = json_decode($json, true);
if (!is_array($industries)) {
    fwrite(STDERR, "Invalid JSON in {$jsonPath}\n");
    exit(1);
}

try {
    $adapter = new Laminas\Db\Adapter\Adapter($db);
    $sql     = new Laminas\Db\Sql\Sql($adapter);
    $time    = time();

    // Remove existing meta-industry rows so we can re-seed idempotently
    $adapter->query(
        "DELETE FROM `content_item` WHERE `type` = 'meta-industry'",
        $adapter::QUERY_MODE_EXECUTE
    );
    echo "Cleared existing meta-industry rows.\n";

    $industryIds = [];
    foreach ($industries as $ind) {
        $key   = $ind['key'] ?? '';
        $title = $ind['title'] ?? '';
        if ($key === '') {
            continue;
        }
        $slug = 'meta-industry-' . $key;
        $insert = new Laminas\Db\Sql\Insert('content_item');
        $insert->values([
            'parent_id'   => 0,
            'title'       => $title,
            'slug'        => $slug,
            'type'        => 'meta-industry',
            'status'      => 1,
            'user_id'     => 0,
            'time_create' => $time,
            'time_update' => $time,
            'time_delete' => 0,
            'information' => '{}',
            'priority'    => 0,
        ]);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result    = $statement->execute();
        $industryIds[$key] = (int) $result->getGeneratedValue();
        echo "Industry: {$key} (id={$industryIds[$key]})\n";
    }

    foreach ($industries as $ind) {
        $parentKey = $ind['key'] ?? '';
        $parentId  = $industryIds[$parentKey] ?? 0;
        if ($parentId === 0) {
            continue;
        }
        $subs = $ind['subcategories'] ?? [];
        foreach ($subs as $sub) {
            $subKey   = $sub['key'] ?? '';
            $subTitle = $sub['title'] ?? '';
            if ($subKey === '') {
                continue;
            }
            $slug = 'meta-industry-' . $parentKey . '-' . $subKey;
            $insert = new Laminas\Db\Sql\Insert('content_item');
            $insert->values([
                'parent_id'   => $parentId,
                'title'       => $subTitle,
                'slug'        => $slug,
                'type'        => 'meta-industry',
                'status'      => 1,
                'user_id'     => 0,
                'time_create' => $time,
                'time_update' => $time,
                'time_delete' => 0,
                'information' => '{}',
                'priority'    => 0,
            ]);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            echo "  Sub: {$subKey} (parent={$parentId})\n";
        }
    }

    echo "Done. Industries and sub-industries seeded from {$jsonPath}\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
