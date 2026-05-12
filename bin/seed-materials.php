<?php

/**
 * Seed content_item with materials from user/data/industries.json.
 * Each subcategory has materials; each material gets sub_industries_keys from the subcategory key.
 * Duplicate material keys (same key in multiple subcategories) are merged into one row with multiple sub_industries_keys.
 * Run from backend: php module/Content/bin/seed-materials.php
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
    fwrite(STDERR, "Set MYSQL_DB_HOST, MYSQL_DB_USER, MYSQL_DB_PASSWORD, MYSQL_DB_NAME.\n");
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
    fwrite(STDERR, "Invalid JSON.\n");
    exit(1);
}

// Build unique materials by key; merge sub_industries_keys when same material appears in multiple subcategories
$materialMap = [];
foreach ($industries as $ind) {
    $subs = $ind['subcategories'] ?? [];
    foreach ($subs as $sub) {
        $subKey = $sub['key'] ?? '';
        if ($subKey === '') {
            continue;
        }
        $materials = $sub['materials'] ?? [];
        foreach ($materials as $m) {
            $key = $m['key'] ?? '';
            if ($key === '') {
                continue;
            }
            if (!isset($materialMap[$key])) {
                $materialMap[$key] = [
                    'name' => $m['name'] ?? '',
                    'title' => $m['title'] ?? '',
                    'key' => $key,
                    'value' => $m['value'] ?? '',
                    'sub_industries_keys' => [],
                ];
            }
            if (!in_array($subKey, $materialMap[$key]['sub_industries_keys'], true)) {
                $materialMap[$key]['sub_industries_keys'][] = $subKey;
            }
        }
    }
}

try {
    $adapter = new Laminas\Db\Adapter\Adapter($db);
    $sql     = new Laminas\Db\Sql\Sql($adapter);
    $time    = time();

    $adapter->query(
        "DELETE FROM `content_item` WHERE `type` = 'material'",
        $adapter::QUERY_MODE_EXECUTE
    );
    echo "Cleared existing material rows.\n";

    foreach ($materialMap as $key => $m) {
        $slug = $key;
        $information = json_encode([
            'name' => $m['name'],
            'title' => $m['title'],
            'key' => $m['key'],
            'value' => $m['value'],
            'sub_industries_keys' => $m['sub_industries_keys'],
        ], JSON_UNESCAPED_UNICODE);

        $insert = new Laminas\Db\Sql\Insert('content_item');
        $insert->values([
            'parent_id'   => 0,
            'title'      => $m['title'],
            'slug'       => $slug,
            'type'       => 'material',
            'status'     => 1,
            'user_id'    => 0,
            'time_create' => $time,
            'time_update' => $time,
            'time_delete' => 0,
            'information' => $information,
            'priority'   => 0,
        ]);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
        echo "Material: {$key} (" . count($m['sub_industries_keys']) . " sub-industries)\n";
    }

    echo "Done. " . count($materialMap) . " materials seeded.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
