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

    protected array $allowType = [
         'category', 'location', 'product', 'video', 'business', 'blog'
    ];

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

    // ToDo: update it
    public function editItem($params, $account)
    {
        $params["time_deleted"] = time();
        return $this->itemRepository->editItem($params, $account);
    }

    // ToDo: update it
    public function addItem($params,)
    {
        return $this->itemRepository->addItem($params);
    }

    // ToDo: update it
    public function deleteItem($params, $account)
    {
        $params["time_deleted"] = time();
        return $this->itemRepository->deleteItem($params);
    }

    public function canonizeItem($item): array
    {
        if (empty($item)) {
            return [];
        }

        if (is_object($item)) {
            $item = [
                'id'                  => $item->getId(),
                'title'                  => $item->getTitle(),
                'slug'                  => $item->getSlug(),
                'type'                  => $item->getType(),
                'status'                  => $item->getStatus(),
                'user_id'             => $item->getUserId(),
                'time_create'             => $item->getTimeCreate(),
                'time_update'             => $item->getTimeUpdate(),
                'time_delete'             => $item->getTimeDelete(),
                'information'             => $item->getInformation(),
            ];
        } else {
            $item = [
                'id'                  => $item['id'],
                'title'             => $item['title'],
                'slug'             => $item['slug'],
                'type'             => $item['type'],
                'status'             => $item['status'],
                'user_id'             => $item['user_id'],
                'time_create'             => $item['time_create'],
                'time_update'             => $item['time_update'],
                'time_delete'             => $item['time_delete'],
                'information'             => $item['information'],
            ];
        }

        return $item;
    }

}
