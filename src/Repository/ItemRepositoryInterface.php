<?php

namespace Content\Repository;

interface ItemRepositoryInterface
{
    public function getItemList($params);
    public function getItem($params);
    public function editItem($params,$account);
    public function addItem($params);
    public function deleteItem($params);
}
