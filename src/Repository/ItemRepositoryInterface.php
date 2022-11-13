<?php

namespace Content\Repository;

interface ItemRepositoryInterface
{
    public function getItemList($params);
    public function getItem($params);
    public function updateItem($params,$account);
    public function storeItem($params);
    public function deleteItem($params);
}
