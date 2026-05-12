<?php

/**
 * Sync suppliers (content_item ids 336, 337, 338):
 * - Ensure each has admin_id in information (supplier admin user id).
 * - For each supplier: if no user linked, find or create user in user_account/user_profile, add supplier role.
 * - Set user_profile.information.company_id = content item id (supplier record id).
 * - Update content_item.information with admin_id.
 *
 * Run from backend directory:
 *   php module/Content/bin/sync-suppliers-336-337-338.php
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

$contentIds = [336, 337, 338];
$defaultPassword = 'Supplier@123';
$credentialHash = password_hash($defaultPassword, PASSWORD_BCRYPT);
$time = time();

try {
    $adapter = new Laminas\Db\Adapter\Adapter($db);
    $sql     = new Laminas\Db\Sql\Sql($adapter);

    $adapter->query(
        "INSERT IGNORE INTO `role_resource` (`name`, `title`, `status`, `section`) VALUES ('supplier', 'Supplier', 1, 'api')",
        $adapter::QUERY_MODE_EXECUTE
    );

    $sel = $sql->select('content_item')
        ->columns(['id', 'information', 'title', 'slug'])
        ->where(['id' => $contentIds, 'type' => 'supplier']);
    $res = $adapter->query($sql->buildSqlString($sel), $adapter::QUERY_MODE_EXECUTE);

    foreach ($res as $row) {
        $itemId = (int) $row['id'];
        $info   = $row['information'] ? json_decode($row['information'], true) : [];
        if (!is_array($info)) {
            $info = [];
        }

        $adminUserId = null;
        if (!empty($info['admin_id'])) {
            $adminUserId = (int) $info['admin_id'];
        } elseif (!empty($info['supplier_admin_user_id'])) {
            $adminUserId = (int) $info['supplier_admin_user_id'];
        }

        $adminIdentity = $info['admin_identity'] ?? '';
        $adminEmail    = $info['admin_email'] ?? '';
        $adminFirstName = $info['admin_first_name'] ?? '';
        $adminLastName  = $info['admin_last_name'] ?? '';

        if ($adminUserId <= 0 && $adminIdentity === '' && $adminEmail === '') {
            echo "Skip content_item id={$itemId}: no admin_id and no admin_identity/admin_email in information.\n";
            continue;
        }

        if ($adminUserId <= 0) {
            $userRow = null;
            if ($adminIdentity !== '') {
                $selUser = $sql->select('user_account')->columns(['id'])->where(['identity' => $adminIdentity]);
                $r = $adapter->query($sql->buildSqlString($selUser), $adapter::QUERY_MODE_EXECUTE)->current();
                if ($r) {
                    $userRow = $r;
                }
            }
            if (!$userRow && $adminEmail !== '') {
                $selUser = $sql->select('user_account')->columns(['id'])->where(['email' => $adminEmail]);
                $r = $adapter->query($sql->buildSqlString($selUser), $adapter::QUERY_MODE_EXECUTE)->current();
                if ($r) {
                    $userRow = $r;
                }
            }

            if ($userRow && !empty($userRow['id'])) {
                $adminUserId = (int) $userRow['id'];
                echo "Found existing user id={$adminUserId} for content_item id={$itemId} (identity/email).\n";
            }
            if ($adminUserId <= 0) {
                $name = trim($adminFirstName . ' ' . $adminLastName) ?: ($info['company_name'] ?? $row['title'] ?? 'Supplier');
                $identity = $adminIdentity ?: ('supplier-' . $itemId . '-' . $time);
                $email = $adminEmail ?: ('supplier' . $itemId . '@supplier.local');

                $insAccount = new Laminas\Db\Sql\Insert('user_account');
                $insAccount->values([
                    'name'         => $name,
                    'identity'     => $identity,
                    'email'        => $email,
                    'credential'   => $credentialHash,
                    'status'       => 1,
                    'time_created' => $time,
                ]);
                $stmt   = $sql->prepareStatementForSqlObject($insAccount);
                $result = $stmt->execute();
                $adminUserId = (int) $result->getGeneratedValue();

                $profileInfo = ['company_id' => $itemId];
                $insProfile = new Laminas\Db\Sql\Insert('user_profile');
                $insProfile->values([
                    'user_id'     => $adminUserId,
                    'first_name'  => $adminFirstName,
                    'last_name'   => $adminLastName,
                    'information' => json_encode($profileInfo, JSON_UNESCAPED_UNICODE),
                ]);
                $sql->prepareStatementForSqlObject($insProfile)->execute();

                foreach (['member', 'supplier'] as $role) {
                    $insRole = new Laminas\Db\Sql\Insert('role_account');
                    $insRole->values(['user_id' => $adminUserId, 'role' => $role, 'section' => 'api']);
                    $sql->prepareStatementForSqlObject($insRole)->execute();
                }
                echo "Created user id={$adminUserId} (identity={$identity}, email={$email}) for content_item id={$itemId}.\n";
            }
        }

        if ($adminUserId <= 0) {
            continue;
        }

        $profileSel = $sql->select('user_profile')->columns(['id', 'information'])->where(['user_id' => $adminUserId]);
        $profileRow = $adapter->query($sql->buildSqlString($profileSel), $adapter::QUERY_MODE_EXECUTE)->current();
        $profileInfo = [];
        if ($profileRow && !empty($profileRow['information'])) {
            $profileInfo = json_decode($profileRow['information'], true);
            if (!is_array($profileInfo)) {
                $profileInfo = [];
            }
        }
        $profileInfo['company_id'] = $itemId;
        $infoJson = json_encode($profileInfo, JSON_UNESCAPED_UNICODE);

        if ($profileRow && !empty($profileRow['id'])) {
            $updProfile = new Laminas\Db\Sql\Update('user_profile');
            $updProfile->set(['information' => $infoJson])->where(['user_id' => $adminUserId]);
            $sql->prepareStatementForSqlObject($updProfile)->execute();
            echo "Updated user_profile company_id={$itemId} for user id={$adminUserId}.\n";
        } else {
            $insProfile = new Laminas\Db\Sql\Insert('user_profile');
            $insProfile->values([
                'user_id'     => $adminUserId,
                'first_name'  => $adminFirstName,
                'last_name'   => $adminLastName,
                'information' => $infoJson,
            ]);
            $sql->prepareStatementForSqlObject($insProfile)->execute();
            echo "Inserted user_profile with company_id={$itemId} for user id={$adminUserId}.\n";
        }

        $info['admin_id'] = $adminUserId;
        $info['supplier_admin_user_id'] = $adminUserId;
        $updItem = new Laminas\Db\Sql\Update('content_item');
        $updItem->set(['information' => json_encode($info, JSON_UNESCAPED_UNICODE), 'time_update' => $time])->where(['id' => $itemId]);
        $sql->prepareStatementForSqlObject($updItem)->execute();
        echo "Updated content_item id={$itemId} with admin_id={$adminUserId}.\n";
    }

    echo "Done. Supplier records 336, 337, 338 synced; company_id set in user_profile.information for each admin.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
