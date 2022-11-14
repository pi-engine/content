<?php

namespace Content\Repository;

use Laminas\Db\ResultSet\HydratingResultSet;

interface ItemRepositoryInterface
{
    public function getItemList($params): HydratingResultSet|array;

    public function getItem($parameter, $type): object|array;

    public function getItemCount(array $params = []) : int;

    public function addItem($params, $account);

    public function editItem($params, $account);

    public function deleteItem($params, $account);
}
