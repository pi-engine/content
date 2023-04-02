<?php

namespace Content\Repository;

use Laminas\Db\ResultSet\HydratingResultSet;

interface LogRepositoryInterface
{
    public function getLog($parameter, $type): object|array;

    public function getLogList(array $params = []): HydratingResultSet|array;

    public function getLogCount(array $params = []): int;

    public function addLog(array $params): object|array;






}
