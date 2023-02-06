<?php

namespace Content\Service;

use Content\Repository\ItemRepositoryInterface;
use function explode;
use function in_array;
use function is_object;
use function json_decode;

class ItemService implements ServiceInterface
{
    /* @var ItemRepositoryInterface */
    protected ItemRepositoryInterface $itemRepository;
    protected array $allowKey
        = [
            'type', 'category', 'brand', 'min_price', 'max_price', 'title', 'color', 'size',
        ];

    // ToDo: get it from DB and cache

    /**
     * @param ItemRepositoryInterface $itemRepository
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository
    )
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getItemList(array $params): array
    {
        $limit = $params['limit'] ?? 25;
        $page = $params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;

        // Set filters
        //$filters = $this->prepareFilter($params);
        $filters = [];

        // Set params
        $listParams = [
            'order' => $order,
            'offset' => $offset,
            'limit' => $limit,
            'type' => $params['type'],
            'status' => 1,
        ];

        // Get filtered IDs
        $itemIdList = [];
        if (!empty($filters)) {
            $rowSet = $this->itemRepository->getIDFromFilter($filters);
            foreach ($rowSet as $row) {
                $itemIdList[] = $this->canonizeMetaItemID($row);
            }
        }

        // Set filtered IDs to params
        if (!empty($itemIdList)) {
            $listParams['id'] = $itemIdList;
        }

        // Get list
        $list = [];
        $rowSet = $this->itemRepository->getItemList($listParams);
        foreach ($rowSet as $row) {
            $list[] = $this->canonizeItem($row);
        }

        // Get count
        $count = $this->itemRepository->getItemCount($listParams);

        return [
            'result' => true,
            'data' => [
                'list' => $list,
                'paginator' => [
                    'count' => $count,
                    'limit' => $limit,
                    'page' => $page,
                ],
                'filters' => $filters,
            ],
            'error' => [],
        ];
    }

    public function canonizeMetaItemID(object|array $meta): int
    {
        if (empty($meta)) {
            return 0;
        }

        if (is_object($meta)) {
            $itemID = $meta->getItemID();
        } else {
            $itemID = $meta['item'];
        }

        return $itemID;
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
                'id' => $item->getId(),
                'title' => $item->getTitle(),
                'slug' => $item->getSlug(),
                'type' => $item->getType(),
                'status' => $item->getStatus(),
                'user_id' => $item->getUserId(),
                'time_create' => $item->getTimeCreate(),
                'time_update' => $item->getTimeUpdate(),
                'time_delete' => $item->getTimeDelete(),
                'information' => $item->getInformation(),
            ];
        } else {
            $item = [
                'id' => $item['id'],
                'title' => $item['title'],
                'slug' => $item['slug'],
                'type' => $item['type'],
                'status' => $item['status'],
                'user_id' => $item['user_id'],
                'time_create' => $item['time_create'],
                'time_update' => $item['time_update'],
                'time_delete' => $item['time_delete'],
                'information' => $item['information'],
            ];
        }

        // Set information
        return !empty($item['information']) ? json_decode($item['information'], true) : [];
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
     * @param string $parameter
     * @param string $type
     *
     * @return array
     */
    public function getItemFilter($where): array
    {
        $item = $this->itemRepository->getItemFilter($where);
        return $this->canonizeItem($item);
    }

    public function prepareFilter($params): array
    {
        // Set filter list
        $filters = [];
        foreach ($params as $key => $value) {
            if (in_array($key, $this->allowKey)) {
                // ToDo: get this info from DB
                switch ($key) {
                    case 'color':
                    case 'size':
                        $filters[$key] = [
                            'key' => $key,
                            'value' => explode(',', $value),
                            'type' => 'string',
                        ];
                        break;

                    case 'type':
                        $filters[$key] = [
                            'key' => $key,
                            'value' => $value,
                            'type' => 'string',
                        ];
                        break;

                    case 'title':
                        $filters[$key] = [
                            'key' => $key,
                            'value' => $value,
                            'type' => 'search',
                        ];
                        break;

                    case 'brand':
                    case 'category':
                        $filters[$key] = [
                            'key' => $key,
                            'value' => $value,
                            'type' => 'int',
                        ];
                        break;

                    case 'max_price':
                        $filters[$key] = [
                            'key' => $key,
                            'value' => $value,
                            'type' => 'rangeMax',
                        ];
                        break;

                    case 'min_price':
                        $filters[$key] = [
                            'key' => $key,
                            'value' => $value,
                            'type' => 'rangeMin',
                        ];
                        break;
                }
            }
        }

        return $filters;
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


    // ToDo: update it
    public function addCartItem($params, $account)
    {
        $product = $this->getItem($params["information"]);
        $cart = $this->getItemFilter(["type" => "cart", "user_id" => $account["id"]]);
        $product["count"] = (int)$params["count"];
        if (sizeof($cart) == 0) {

            $param = [
                "id" => null,
                "title" => "cart",
                "slug" => "cart",
                "type" => "cart",
                "status" => 1,
                "user_id" => $params["user_id"],
                "information" => json_encode([$product]),
            ];
            $this->itemRepository->addCartItem($param);
        } else {

            $index = $this->checkObjectInArray($cart, $product);

            if ($index > -1) {
                $cart[$index]["count"] = (int)$cart[$index]["count"] + (int)$params["count"];
            }else{
                $cart[] = $product;
            }


            $param = [
                "id" => null,
                "title" => "cart",
                "slug" => "cart",
                "type" => "cart",
                "status" => 1,
                "user_id" => $params["user_id"],
                "information" => json_encode($cart),
            ];

            $this->itemRepository->deleteCart("cart", "type");
            $this->itemRepository->addCartItem($param);
        }

//        return $this->itemRepository->addCartItem($params, $account);
    }

    private function checkObjectInArray(array $array, array $object, $key = "id")
    {
        $index = -1;
        foreach ($array as $item) {
            $index++;
            if ($item[$key] == $object[$key]) {
                return $index;
            }
        }
    }
}
