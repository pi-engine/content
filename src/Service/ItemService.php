<?php

namespace Content\Service;

use Content\Repository\ItemRepositoryInterface;
use function json_decode;

class ItemService implements ServiceInterface
{
    /* @var ItemRepositoryInterface */
    protected ItemRepositoryInterface $itemRepository;

    /**
     * @param ItemRepositoryInterface $itemRepository
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository
    ) {
        $this->itemRepository = $itemRepository;
    }

    protected array $allowType
        = [
            'category', 'location', 'product', 'video', 'business', 'blog',
        ];

    /**
     * @param array $params
     *
     * @return array
     */
    public function getItemList(array $params): array
    {
        $limit  = $params['limit'] ?? 25;
        $page   = $params['page'] ?? 1;
        $order  = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;

        // ToDo: add filter
        $listParams = [
            'order'  => $order,
            'offset' => $offset,
            'limit'  => $limit,
            'type'   => $params['type'],
            'status' => 1,
        ];

        // Get list
        $list   = [];
        $rowSet = $this->itemRepository->getItemList($listParams);
        foreach ($rowSet as $row) {
            $list[] = $this->canonizeItem($row);
        }

        // Get count
        $count = $this->itemRepository->getItemCount($listParams);

        return [
            'result' => true,
            'data'   => [
                'list'      => $list,
                'paginator' => [
                    'count' => $count,
                    'limit' => $limit,
                    'page'  => $page,
                ],
            ],
            'error'  => [],
        ];
    }

    /**
     * @param string $parameter
     * @param string $type
     *
     * @return array
     */
    public function getItem(string $parameter, string $type = 'id'): array
    {
        $item = $this->itemRepository->getItem($parameter, $type);
        return $this->canonizeItem($item);
    }

    /**
     * @param object|array $item
     *
     * @return array
     */
    public function canonizeItem(object|array $item): array
    {
        if (empty($item)) {
            return [];
        }

        if (is_object($item)) {
            $item = [
                'id'          => $item->getId(),
                'title'       => $item->getTitle(),
                'slug'        => $item->getSlug(),
                'type'        => $item->getType(),
                'status'      => $item->getStatus(),
                'user_id'     => $item->getUserId(),
                'time_create' => $item->getTimeCreate(),
                'time_update' => $item->getTimeUpdate(),
                'time_delete' => $item->getTimeDelete(),
                'information' => $item->getInformation(),
            ];
        } else {
            $item = [
                'id'          => $item['id'],
                'title'       => $item['title'],
                'slug'        => $item['slug'],
                'type'        => $item['type'],
                'status'      => $item['status'],
                'user_id'     => $item['user_id'],
                'time_create' => $item['time_create'],
                'time_update' => $item['time_update'],
                'time_delete' => $item['time_delete'],
                'information' => $item['information'],
            ];
        }

        // Set information
        return !empty($item['information']) ? json_decode($item['information'], true) : [];
    }


    // ToDo: update it
    public function editItem($params, $account)
    {
        $params["time_deleted"] = time();
        return $this->itemRepository->editItem($params, $account);
    }

    // ToDo: update it
    public function addItem($params, $account)
    {
        return $this->itemRepository->addItem($params, $account);
    }

    // ToDo: update it
    public function deleteItem($params, $account)
    {
        $params["time_deleted"] = time();
        return $this->itemRepository->deleteItem($params, $account);
    }
}
