<?php

namespace Content\Service;

use Content\Repository\ItemRepositoryInterface;

class ItemService implements ServiceInterface
{
    /* @var ItemRepositoryInterface */
    protected ItemRepositoryInterface $itemRepository;


    /**
     * @param ItemRepositoryInterface $itemRepository
     */
    public function __construct(
        ItemRepositoryInterface       $itemRepository
    )
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param $params
     * @param $account
     *
     * @return array
     */
    public function getItemList($params )
    {
        // Get items list
        $limit = (int)($params['limit'] ?? 10);
        $page = (int)($params['page'] ?? 1);
        $type = $params['type'] ??  "item";
        $parent_id = (int)($params['parent_id'] ?? 0);
        $item = $params['item'] ?? ['created_at DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;

        // Set params
        $listParams = [
            'page' => $page,
            'parent_id' => $parent_id,
            'limit' => $limit,
            'item' => $item,
            'offset' => $offset,
            'type' => $type,
        ];
        return $this->itemRepository->getItemList($listParams);

    }

    public function getItem($params, $account)
    {
        return $this->itemRepository->getItem($params);
    }

    public function updateItem($params, $account)
    {
        $params["time_deleted"] = time();
        return $this->itemRepository->updateItem($params, $account);
    }

    public function storeItem($params,)
    {
        return $this->itemRepository->storeItem($params);
    }



    public function deleteItem($params, $account)
    {
        $params["time_deleted"] = time();
        return $this->itemRepository->deleteItem($params);
    }

}
