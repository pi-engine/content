<?php

namespace Content\Service;

use Club\Service\ScoreService;
use Content\Repository\ItemRepositoryInterface;
use mysql_xdevapi\Exception;
use Notification\Service\NotificationService;
use User\Service\AccountService;

use User\Service\UtilityService;
use function explode;
use function in_array;
use function is_object;
use function json_decode;

class ItemService implements ServiceInterface
{


    /** @var AccountService */
    protected AccountService $accountService;

    /** @var UtilityService */
    protected UtilityService $utilityService;

    /** @var ScoreService */
    protected ScoreService $scoreService;

    /** @var LogService */
    protected LogService $logService;

    /** @var NotificationService */
    protected NotificationService $notificationService;

    /* @var ItemRepositoryInterface */
    protected ItemRepositoryInterface $itemRepository;
    protected array $allowKey
        = [
            'category',
            'brand',
            'min_price',
            'max_price',
            'title',
            'color',
            'destination',
            'duration',
            'size',
        ];

    // ToDo: get it from DB and cache

    /* @var array */
    protected array $config;

    /**
     * @param ItemRepositoryInterface $itemRepository
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository,
        AccountService          $accountService,
        ScoreService            $scoreService,
        NotificationService     $notificationService,
        LogService              $logService,
        UtilityService          $utilityService,
                                $config
    )
    {
        $this->itemRepository = $itemRepository;
        $this->accountService = $accountService;
        $this->scoreService = $scoreService;
        $this->notificationService = $notificationService;
        $this->logService = $logService;
        $this->utilityService = $utilityService;
        $this->config = $config;
    }


    /**
     * @param array $params
     *
     * @return array
     */
    public function getItemList(array $params): array
    {
        ///TODO:update limit count
        $limit = $params['limit'] ?? 125;
        $page = $params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;

        // Set filters
        $filters = $this->prepareFilter($params);

        // Set params
        $listParams = [
            'order' => $order,
            'offset' => $offset,
            'limit' => $limit,
            'type' => $params['type'],
            'status' => 1,
        ];


        if (isset($params['data_from'])) {
            $listParams['data_from'] = strtotime(
                ($params['data_from']) != null
                    ? sprintf('%s 00:00:00', $params['data_from'])
                    : sprintf('%s 00:00:00', date('Y-m-d', strtotime('-1 month')))
            );
        }

        if (isset($params['data_to'])) {
            $listParams['data_to'] = strtotime(
                ($params['data_to']) != null
                    ? sprintf('%s 00:00:00', $params['data_from'])
                    : sprintf('%s 23:59:59', date('Y-m-d'))
            );
        }

        if (isset($params['user_id'])) {
            $listParams['user_id'] = $params['user_id'];
        }

        if (isset($params['title'])) {
            $listParams['title'] = $params['title'];
        }


        $itemIdList = [];
        if (!empty($filters)) {
            $rowSet = $this->itemRepository->getIDFromFilter($filters);
            foreach ($rowSet as $row) {
                $itemIdList[] = $this->canonizeMetaItemID($row);
            }
            $listParams['id'] = $itemIdList;
        }

        $list = [];
        $rowSet = $this->itemRepository->getItemList($listParams);
        foreach ($rowSet as $row) {
            $list[] = $this->canonizeItem($row, $params['type']);
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


    /**
     * @param array $params
     *
     * @return array
     */
    public function getCartList(array $params): array
    {
        $limit = $params['limit'] ?? 25;
        $page = $params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;


        // Set params
        $params['order'] = $order;
        $params['offset'] = $offset;
        $params['limit'] = $limit;
        $params['type'] = $params['type'];
        $params['status'] = 1;


        $rowSet = $this->itemRepository->getItemList($params);
        foreach ($rowSet as $row) {
            ///TODO: review this codes
            $list[] = $this->canonizeItem($row);
            $list = $this->canonizeItem($row);
        }


        $count = $this->itemRepository->getItemCount($params);

        return [
            'result' => true,
            'data' => [
                'list' => $list ?? [],
                'paginator' => [
                    'count' => $count,
                ],
            ],
            'error' => [],
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getCart(array $params): array
    {
        $limit = $params['limit'] ?? 25;
        $page = $params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;


        // Set params
        $params['order'] = $order;
        $params['offset'] = $offset;
        $params['limit'] = $limit;
        $params['type'] = $params['type'];
        $params['status'] = 1;

        $list = [];
        $rowSet = $this->itemRepository->getItemList($params);
        foreach ($rowSet as $row) {
            $list = $this->canonizeItem($row);
        }
        return $list;
    }

    public function canonizeMetaItemID(object|array $meta): int
    {
        if (empty($meta)) {
            return 0;
        }

        if (is_object($meta)) {
            $itemID = $meta->getItemID();
        } else {
            $itemID = $meta['item_id'];
        }

        return $itemID;
    }

    /**
     * @param object|array $item
     *
     * @return array
     */
    public function canonizeItem(object|array $item, $type = 'global'): array
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

        $data = !empty($item['information']) ? json_decode($item['information'], true) : [];

        switch ($type) {
            case 'tour':
                $data['type'] = 'tour';
                break;
            case 'product':
                $data['price'] = 1000;
                $data['price_view'] = '1,000,000 تومان';
                $data['stock_status'] = 1;
                $data['stock_status_view'] = 'موجود در انبار';
        }

        // Set information
        return $data;
    }

    /**
     * @param string $parameter
     * @param string $type
     *
     * @return array
     */
    public function getItem(string $parameter, string $type = 'id', $params = []): array
    {
        $item = $this->itemRepository->getItem($parameter, $type, $params);
        return $this->canonizeItem($item, (isset($params['type'])) ? $params['type'] : 'global');
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

                    case 'brand':
//                    case 'destination':
//                    case 'duration':
                    case 'category':
                        $filters[$key] = [
                            'key' => $key,
                            'value' => $value,
                            'type' => 'id',
                        ];
                        break;

                    /*case 'title':
                            $filters[$key] = [
                                'key' => $key,
                                'value' => $value,
                                'type' => 'search',
                            ];
                            break;*/

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

    // TODO: update it
    public function editItem($params, $account = null)
    {
        $params["time_update"] = time();
        return $this->itemRepository->editItem($params);
    }

    // TODO: update it
    public function addItem($params, $account)
    {
        return $this->itemRepository->addItem($params, $account);
//        $paramsBase                = $params;
//        $params['information']     = json_encode($params);
//        $response                  = $this->itemRepository->addItem($params, $account);
//        $paramsBase['id']          = (int)$response->getId();
//        $paramsBase['information'] = json_encode($paramsBase);
//        return $this->canonizeItem($this->itemRepository->editItem($paramsBase));
    }

    // TODO: update it
    public function deleteItem($params, $account)
    {
        $params["time_deleted"] = time();
        return $this->itemRepository->deleteItem($params, $account);
    }

    ///TODO: update it
    public function addCartItem($params, $account)
    {
        $product = $this->getItem($params["information"], "slug");
        $cart = $this->getItemFilter(["type" => "cart", "user_id" => $account["id"]]);
        $product["count"] = (int)$params["count"];
        if (sizeof($cart) == 0) {
            $param = [
                "id" => null,
                "title" => "cart",
                "slug" => "cart-{$account["id"]}",
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
            } else {
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

            $this->itemRepository->destroyItem(["type" => "cart", "user_id" => $params["user_id"]]);
            $this->itemRepository->addCartItem($param);
        }
    }

    // ToDo: update it
    public function updateCart($params, $account)
    {
        $product = $this->getItem($params["slug"], "slug");
        $cart = $this->getItemFilter(["type" => "cart", "user_id" => $account["id"]]);
        $product["count"] = (int)$params["count"];

        $index = $this->checkObjectInArray($cart, $product);

        if ($index > -1) {
            $cart[$index]["count"] = $params["count"];
        } else {
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

        $this->itemRepository->destroyItem(["type" => "cart", "user_id" => $params["user_id"]]);
        $this->itemRepository->addCartItem($param);
    }

    // ToDo: update it
    public function deleteCartItem($params, $account)
    {
        $product = $this->getItem($params["slug"], "slug");
        $cart = $this->getItemFilter(["type" => "cart", "user_id" => $account["id"]]);
        echo sizeof($cart);
        if (sizeof($cart) < 2) {
            $this->itemRepository->destroyItem(["type" => "cart", "user_id" => $account["id"]]);
        } else {
            $index = $this->checkObjectInArray($cart, $product);

            if ($index > -1) {
//                unset($cart[$index]);
                $list = [];
                foreach ($cart as $item) {
                    if ($item["id"] != $cart[$index]["id"]) {
                        $list[] = $item;
                    }
                }
                $cart = $list;
            } else {
                $cart[] = [];
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

            $this->itemRepository->destroyItem(["type" => "cart", "user_id" => $account["id"]]);
            if (sizeof($cart)) {
                $this->itemRepository->addCartItem($param);
            }
        }
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


    // ToDo: update it
    public function addOrderItem($params, $account): array
    {

        $limit = $params['limit'] ?? 25;
        $page = $params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;
        $cart_request = [
            "user_id" => $account["id"],
            "type" => "cart",
            "order" => $order,
            "offset" => $offset,
            "limit" => $limit,
            "status" => 1,

        ];
        $cart = $this->getCart($cart_request);

        if (sizeof($cart) == 0)
            return [];

        $items = [];
        $index = 0;
        foreach ($cart as $item) {
            $items[$index] = $this->getItem($item['slug'], 'slug', ['type' => 'product']);
            $index++;
        }


        ///TODO: get address from database from old address if send address_id
//        if (!isset($params["address_id"]) || $params["address_id"] == null || $params["address_id"] == "null") {
        $address = [
            "id" => null,
            "name" => $params["name"],
            "phone" => $params["phone"],
            "address" => $params["address"],
            "state" => $params["state"],
            "city" => $params["city"],
            "zip_code" => $params["zip_code"],
        ];
        $address_request = [
            "type" => "address",
            "slug" => "address-{$account["id"]}-" . time(),
            "user_id" => $account["id"],
            "status" => 1,
            "title" => "address-{$account["id"]}",
            "information" => json_encode($address),
        ];
        $address["id"] = $this->addItem($address_request, $account)->getId();


        $order_information = [
            "user_id" => $account["id"],
            "status" => "created",
            "date_time" => date('m/d/Y h:i', time()),
            "description" => $params["description"],
            "items" => $items,
            "address" => $address,
        ];

        $slug = "order-{$account["id"]}-" . time();
        $order_information['slug'] = $slug;

        $order_request = [
            "type" => "module_order",
            "slug" => $slug,
            "user_id" => $account["id"],
            "status" => 1,
            "title" => "order-{$account["id"]}",
            "information" => json_encode($order_information),
        ];

        $this->itemRepository->destroyItem(["type" => "cart", "user_id" => $account["id"]]);
        return $this->canonizeItem($this->addItem($order_request, $account));
//        }
    }



    ///// Start Question Section /////
    /// services of question type

    public function getGroupItem($params, $type = "id"): array
    {
        $list = [];
        $params = "'" . str_replace(',', "','", $params) . "'";
        $rowSet = $this->itemRepository->getGroupList($params, $type);
        foreach ($rowSet as $row) {
            $list[] = $this->canonizeItem($row);
        }
        return $list;
    }


    // TODO: update it
    public function addQuestion($requestBody): object|array
    {
        $nullObject = [];// new \stdClass();

        $hasCategories = isset($requestBody['categories']);
        $categoryKeyType = $requestBody['category_key_type'] ?? 'id';

        $params = [
            'user_id' => $requestBody['user_id'] ?? 0,
            'title' => $requestBody['title'],
            'slug' => uniqid(),
            'status' => 1,
            'type' => $requestBody['type'] ?? 'question',
            'time_create' => time(),
        ];

        $information = $params;
        $information['time_created_view'] = $this->utilityService->date($params['time_create']);
        $information['extra'] = isset($requestBody['extra']) ? json_decode($requestBody['extra']) : new \stdClass();
        $information['body'] = $nullObject;
        $information['body']['user'] = $params['user_id'] == 0 ? $nullObject : $this->accountService->getProfile($params);
        $information['body']['name'] = $params['user_id'] == 0 ? ''
            : $this->accountService->getProfile($params)["first_name"] . ' ' . $this->accountService->getProfile($params)["last_name"];
        $information['body'] ['description'] = $requestBody['description'] ?? '';
        $information['body']['answer'] = $nullObject;
        $information['meta'] = $nullObject;
        $information['meta']['categories'] = $hasCategories ? $this->getGroupItem($requestBody['categories'], $categoryKeyType) : [];
        $information['meta']['like'] = 0;
        $information['meta']['dislike'] = 0;
        $params['information'] = json_encode($information, JSON_UNESCAPED_UNICODE);

        $question = $this->itemRepository->addItem($params);
        $information = $this->canonizeItem($question);
        $information["id"] = (int)$question->getId();
        $editedQuestion = [
            "id" => (int)$question->getId(),
            "time_update" => time(),
            "information" => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];

        $question = $this->itemRepository->editItem($editedQuestion);

        // add meta record for this question if isset categories parameter
        if (isset($requestBody['categories'])) {
            $metaParams = [
                'item_id' => $question->getId(),
                'meta_key' => 'category',
                'time_create' => time(),
            ];

            $categories = explode(',', $requestBody['categories']);
            foreach ($categories as $category) {
                $metaParams['value_id'] = $category;
                $this->itemRepository->addMetaValue($metaParams);
            }
        }

        return $this->canonizeItem($question);
    }

    public function replyQuestion($params): object|array
    {
        $nullObject = [];// new \stdClass();
        $hasCategories = isset($params['categories']);

        $answer = [
            "user_id" => $params['user_id'] ?? 0,
            "title" => $params['question_slug'],
            "slug" => $params['slug'],
            "status" => 1,
            "type" => $requestBody['type'] ?? 'answer',
            'time_create' => time(),
        ];

        $answerInformation = $answer;
        $answerInformation['time_created_view'] = $this->utilityService->date($answer['time_create']);
        $answerInformation['title'] = $params['title'];
        $information['meta']['categories'] = $hasCategories ? $this->canonizeItem($this->itemRepository->getItem($requestBody['categories'], 'id')) : [];
        $answerInformation['meta']['like'] = 0;
        $answerInformation['meta']['dislike'] = 0;


        $answer["information"] = json_encode($answerInformation, JSON_UNESCAPED_UNICODE);
        $answer = $this->itemRepository->addItem($answer);


        $params["user"] = $params["user_id"] == 0 ? $nullObject : $this->accountService->getProfile($params);
        $question = $this->itemRepository->getItem(str_replace("child_slug_", "", $params["question_slug"]), "slug");


        $information = $this->canonizeItem($question);
        if (sizeof($information) == 0) {
            return [];
        }

        $answerInformation["id"] = (int)$answer->getId();
        array_unshift($information["body"]["answer"], $answerInformation);
        $editedQuestion = [
            "id" => (int)$question->getId(),
            "time_update" => time(),
            "information" => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];

        return $this->canonizeItem($this->itemRepository->editItem($editedQuestion));
    }

    ///// End Question Section /////


    // TODO: update it
    public function addSupport($requestBody): object|array
    {
        $nullObject = [];// new \stdClass();

        $hasCategories = isset($requestBody['categories']);
        $categoryKeyType = $requestBody['category_key_type'] ?? 'id';

        $params = [
            'user_id' => $requestBody['user_id'] ?? 0,
            'title' => $requestBody['title'],
            'slug' => uniqid(),
            'status' => 1,
            'type' => $requestBody['type'] ?? 'question',
            'time_create' => time(),
        ];

        $information = $params;
        $information['time_created_view'] = $this->utilityService->date($params['time_create']);
        $information['extra'] = isset($requestBody['extra']) ? json_decode($requestBody['extra']) : new \stdClass();
        $information['body'] = $nullObject;
        $information['body']['user'] = $params['user_id'] == 0 ? $nullObject : $this->accountService->getProfile($params);
        $information['body']['name'] = $params['user_id'] == 0 ? ''
            : $this->accountService->getProfile($params)["first_name"] . ' ' . $this->accountService->getProfile($params)["last_name"];
        $information['body'] ['description'] = $requestBody['description'] ?? '';
        $information['body']['answer'] = $nullObject;
        $information['meta'] = $nullObject;
        $information['meta']['categories'] = $hasCategories ? $this->getGroupItem($requestBody['categories'], $categoryKeyType) : [];
        $information['meta']['like'] = 0;
        $information['meta']['dislike'] = 0;
        $params['information'] = json_encode($information, JSON_UNESCAPED_UNICODE);

        $support = $this->itemRepository->addItem($params);
        $information = $this->canonizeItem($support);
        $information["id"] = (int)$support->getId();
        $editedSupport = [
            "id" => (int)$support->getId(),
            "time_update" => time(),
            "information" => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];

        $support = $this->itemRepository->editItem($editedSupport);

        // add meta record for this question if isset categories parameter
        if (isset($requestBody['categories'])) {
            $metaParams = [
                'item_id' => $support->getId(),
                'meta_key' => 'category',
                'time_create' => time(),
            ];

            $categories = explode(',', $requestBody['categories']);
            foreach ($categories as $category) {
                $metaParams['value_id'] = $category;
                $this->itemRepository->addMetaValue($metaParams);
            }
        }

        return $this->canonizeItem($support);
    }

    public function replySupport($params, $notificationTypes): object|array
    {
        $support = $this->itemRepository->getItem(str_replace("child_slug_", "", $params["support_slug"]), "slug");
        if ($support == null) {
            return [];
        }

        $nullObject = [];// new \stdClass();
        $hasCategories = isset($params['categories']);

        $answer = [
            "user_id" => $params['user_id'] ?? 0,
            "title" => $params['support_slug'],
            "slug" => $params['slug'],
            "status" => 1,
            "type" => $params['type'] ?? 'answer',
            'time_create' => time(),
        ];

        $answerInformation = $answer;
        $answerInformation['time_created_view'] = $this->utilityService->date($answer['time_create']);
        $answerInformation['user'] = $params['user_id'] == 0 ? $nullObject : $this->accountService->getProfile($params);
        $answerInformation['name'] = $params['user_id'] == 0 ? ''
            : $this->accountService->getProfile($params)["first_name"] . ' ' . $this->accountService->getProfile($params)["last_name"];
        $answerInformation['title'] = $params['title'];
        $information['meta']['categories'] = $hasCategories ? $this->canonizeItem($this->itemRepository->getItem($params['categories'], 'id')) : [];
        $answerInformation['meta']['like'] = 0;
        $answerInformation['meta']['dislike'] = 0;

        $answer["information"] = json_encode($answerInformation, JSON_UNESCAPED_UNICODE);
        $answer = $this->itemRepository->addItem($answer);


        $params["user"] = $params["user_id"] == 0 ? $nullObject : $this->accountService->getProfile($params);


        $information = $this->canonizeItem($support);
        if (sizeof($information) == 0) {
            return [];
        }

        $answerInformation["id"] = (int)$answer->getId();
        array_unshift($information["body"]["answer"], $answerInformation);
        $editedSupport = [
            "id" => (int)$support->getId(),
            "time_update" => time(),
            "information" => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];

        if (sizeof($notificationTypes) > 0) {
            $userAccount = $this->accountService->getAccount(['id' => $information['user_id']]);
            $this->sendNotification($notificationTypes, $userAccount, $params['title']);
        }


        return $this->accountService->getAccount(['id' => $information['user_id']]);
        return $this->canonizeItem($this->itemRepository->editItem($editedSupport));
    }


    /**
     * @param string $parameter
     * @param string $type
     *
     * @return array
     */
    public function getSupport(string $parameter, string $type = 'id', $params = []): array
    {

        $item = $this->itemRepository->getItem($parameter, $type, $params);

        $limit = $params['limit'] ?? 1000;
        $page = $params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;

        $item = $this->canonizeItem($item);

        ///TODO: get answers from db
        if (sizeof($item) > 0) {
            // Set params
            $listParams = [
                'page' => $page,
                'limit' => $limit,
                'order' => $order,
                'offset' => $offset,
                'type' => "answer",
                'title' => 'child_slug_' . $item['slug'],
            ];
            $item['body']['answer'] = [];
            $answers = $this->itemRepository->getItemList($listParams);
            foreach ($answers as $answer) {
                $item['body']['answer'][] = $this->canonizeItem($answer);
            }
        }


        return $item;
    }

    ///// Start Location Section /////
    ///// services of location type
    ///
    public function getMarks($params, $account): array
    {
        $limit = (int)$params['limit'] ?? 1000;
        $page = (int)$params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;

        // Set params
        $listParams = [
            'page' => $page,
            'limit' => $limit,
            'order' => $order,
            'offset' => $offset,
            'type' => "location",
        ];

        //TODO: set filters OR Merge this service with getItemList
        $filters = $this->prepareFilter($params);

        $scores = [];
        foreach ($this->scoreService->getScoreListGroupByItem() as $score) {
            $scores[$score["item_id"]] = $score;
        }
        // Get list
        $list = [];
        $rowSet = $this->itemRepository->getItemList($listParams);
        foreach ($rowSet as $row) {
            $list[] = $this->canonizeItem($row);
        }


        $reserves = $this->getReserveList([], $account);
        $sortedReserves = [];
        if (!empty($reserves)) {
            foreach ($reserves as $reserve) {
                $sortedReserves[$reserve['item_id']][] = $reserve;
            }

        }

        $packages = $this->scoreService->getCustomList([]);
        $sortedPackages = [];
        if (!empty($packages)) {
            if (isset($packages['data']['list'])) {
                $packageList = $packages['data']['list'];
                if (!empty($packageList)) {
                    foreach ($packageList as $package) {
                        $sortedPackages[$package['item_id']][] = $package;
                    }
                }
            }
        }

        $ll = [];
        for ($i = 0; $i < sizeof($list); $i++) {
            $ll[$i] = $list[$i];
            $ll[$i]["score"] = isset($scores[$ll[$i]["id"]]) ? $scores[$ll[$i]["id"]]["score"] : 0;
            $ll[$i]["has_reserve"] = isset($sortedReserves[$list[$i]['id']]) && !empty($sortedReserves[$list[$i]['id']]);
            $ll[$i]["reserves"] = $sortedReserves[$list[$i]['id']] ?? [];
            $ll[$i]["has_package"] = isset($sortedPackages[$list[$i]['id']]) && !empty($sortedPackages[$list[$i]['id']]);
            $ll[$i]["packages"] = $sortedPackages[$list[$i]['id']] ?? [];
            $ll[$i]["classification"] = $this->calculateClassification((int)$ll[$i]["score"]);
        }

        // Get count
        $count = $this->itemRepository->getItemCount($listParams);

        return [
            'result' => true,
            'data' => [
                'list' => $ll,
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

    public function getMark(array $params, $account): array|object
    {
        $item = $this->itemRepository->getItem($params['slug'], 'slug');
        $item = $this->canonizeItem($item);
        $scores = [];
        foreach ($this->scoreService->getScoreListGroupByItem() as $score) {
            $scores[$score["item_id"]] = $score;
        }
        $item["score"] = isset($scores[$item["id"]]) ? $scores[$item["id"]]["score"] : 0;
        $item["classification"] = $this->calculateClassification((int)$item["score"]);

        $reserves = $this->getReserveList([], $account);
        $sortedReserves = [];
        if (!empty($reserves)) {
            foreach ($reserves as $reserve) {
                $sortedReserves[$reserve['item_id']][] = $reserve;
            }
        }

        $packages = $this->scoreService->getCustomList([]);
        $sortedPackages = [];
        if (!empty($packages)) {
            if (isset($packages['data']['list'])) {
                $packageList = $packages['data']['list'];
                if (!empty($packageList)) {
                    foreach ($packageList as $package) {
                        $sortedPackages[$package['item_id']][] = $package;
                    }
                }
            }
        }

        $item["score"] = isset($scores[$item["id"]]) ? $scores[$item["id"]]["score"] : 0;
        $item["has_reserve"] = isset($sortedReserves[$item['id']]) && !empty($sortedReserves[$item['id']]);
        $item["reserves"] = $sortedReserves[$item['id']] ?? [];
        $item["has_package"] = isset($sortedPackages[$item['id']]) && !empty($sortedPackages[$item['id']]);
        $item["packages"] = $sortedPackages[$item['id']] ?? [];
        $item["classification"] = $this->calculateClassification((int)$item["score"]);

        return $item;
    }
    ///// End Location Section /////


    ///// Start Category Section /////
    ///// services of location type
    ///
    public function getCategories($params): array
    {
        $limit = (int)$params['limit'] ?? 1000;
        $page = (int)$params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;

        // Set params
        $listParams = [
            'page' => $page,
            'limit' => $limit,
            'order' => $order,
            'offset' => $offset,
            'type' => "categories",
        ];

        // Get list
        $list = [];
        $rowSet = $this->itemRepository->getItemList($listParams);
        foreach ($rowSet as $row) {
            $list = $this->canonizeItem($row);
        }
        return $list;
    }
    ///// End Category Section /////

    ///// Start Setting Section /////
    ///// Config variable of engin
    ///
    public function getVersion($params): object|array
    {
        try {
            $platform = $params["platform"];
            $config = $platform == "api"
                ?
                $this->config[$platform]
                :
                $this->config["application"][$platform];
            return [
                "status" => $config["status"],
                "last_version" => $config["last_version"],
                "url" => $config["url"],
                "is_force" => !in_array($params["version"], $config["authorized_versions"]),
                "message" => $config["message"],
                "current_version" => $params["version"],
                "button_title" => $config["button_title"],
                "title" => $config["title"],

            ];
        } catch (Exception $error) {
            return [];
        }
    }

    ///// End Setting Section /////
    private function calculateClassification(mixed $score): string
    {
        if ($score < 100) {
            return 1;
        } elseif ($score < 300) {
            return 2;
        } elseif ($score < 700) {
            return 3;
        } else {
            return 4;
        }
    }


    ///// Start Reservation Section /////
    ///// Config variable of engin
    ///

    /**
     * @param array $params
     *
     * @return array
     */
    public function getReserveList(array $params, $account): array
    {
        ///TODO:update limit count
        $limit = $params['limit'] ?? 125;
        $page = $params['page'] ?? 1;
        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        $offset = ($page - 1) * $limit;
        if (isset($params["role"]) && $params["role"] == "owner") {
            $title = "reservation_owner_" . $this->accountService->getProfile(["user_id" => $account["id"]])["item_id"] . "_";
        } else {
            $title = "reservation_customer_" . $account["id"] . "_";
        }


        // Set params
        $listParams = [
            'order' => $order,
            'offset' => $offset,
            'limit' => $limit,
            'type' => $params['type'] ?? 'reservation',
            'status' => 1,
            'title' => $title,
        ];

        // Get list
        $list = [];
        $rowSet = $this->itemRepository->getItemList($listParams);

        foreach ($rowSet as $row) {
            $list[] = $this->canonizeItem($row);
        }

        return $list;
    }


    ///TODO: set title for slug =>
    public function reserve(object|array|null $params, $account): array
    {
        $sameCodeFlag = false;

        ///check code is valid
        $customList = $this->scoreService->getActiveCustomList(
            [
                "item_id" => $params["item_id"],
                "code" => $params["code"]
            ]
        );

        ///TODO: set error handler for when the code or item_id is not valid
        if (sizeof($customList) == 0) {
            return ["id" => -1];
        }
        $custom = $customList[0];

        $customerTitle = $params["type"] . "_customer_" . $params["user_id"] . "_owner_" . $params["item_id"] . "_";
        $customerSlug = $params["type"] . "_customer_" . $params["user_id"] . "_" . $custom["code"];


        $ownerTitle = $params["type"] . "_owner_" . $params["item_id"] . "_";
        $ownerSlug = $customerTitle;

        $ownerReserved = $this->canonizeItem($this->itemRepository->getItem($ownerSlug, 'slug'));
        $customerReserved = $this->canonizeItem($this->itemRepository->getItem($customerTitle, 'title'));

        $expired = strtotime("+1 hour");

        $customerParam = $this->getReserveParams($customerSlug, $params, $custom["code"], $expired, $customerTitle);
        $ownerParam = $this->getReserveParams($ownerSlug, $params, $custom["code"], $expired, $ownerTitle);


        /// if array have any user with the same slug
        if (!empty($ownerReserved)) {
            if ($custom['code'] !== $customerReserved['code']) {

                /// get data of old score code from customer
                $oldCustomList = $this->scoreService->getActiveCustomList(
                    [
                        "item_id" => $params["item_id"],
                        "code" => $customerReserved["code"]
                    ]
                );
                /// remove the used report of score for this customer
                $this->scoreService->updateCustom(
                    [
                        'code' => $oldCustomList[0]['code'],
                    ],
                    [
                        'count_used' => $oldCustomList[0]['count_used'] - 1,
                    ]
                );
                $this->itemRepository->destroyItem(['slug' => $customerReserved['slug']]);
                $this->itemRepository->destroyItem(['slug' => $ownerReserved['slug']]);
            } else {
                $sameCodeFlag = true;
            }
        }


        /// check that the code sent not equal the old value
        if (!$sameCodeFlag) {
            $this->itemRepository->addItem($ownerParam);
            $this->itemRepository->addItem($customerParam);
            $this->scoreService->updateCustom(
                [
                    'code' => $custom['code'],
                ],
                [
                    'count_used' => $custom['count_used'] + 1,
                ]
            );

            /// send notification
            ///Send notification to owner
            $ownerProfile = $this->accountService->getProfile(['item_id' => $params["item_id"]]);
            if (isset($ownerProfile['user_id'])) {
                $owner = $this->accountService->getUserFromCacheFull($ownerProfile['user_id']);

                $notificationParams = [
                    'information' =>
                        [
                            "device_token" => $owner['device_tokens'],
                            "in_app" => true,
                            "in_app_title" => 'Reservation',
                            "title" => 'Reservation',
                            "in_app_body" => 'You have been reserved by a user . package code is ' . $custom['code'] . ' ',
                            "body" => 'You have been reserved by a user . package code is ' . $custom['code'] . ' ',
                            "event" => 'reservation',
                            "user_id" => (int)$ownerProfile['user_id'],
                            "item_id" => (int)$params['item_id'],
                            "sender_id" => 0,
                            "type" => 'info',
                            "image_url" => '',
                            "receiver_id" => (int)$ownerProfile['user_id'],
                        ],
                ];
                $notificationParams['push'] = $notificationParams['information'];
                $this->notificationService->send($notificationParams, 'owner');
            }


            ///Send notification to customer
            $customer = $this->accountService->getUserFromCacheFull($params["user_id"]);

            $notificationParams = [
                'information' =>
                    [
                        "device_token" => $customer['device_tokens'],
                        "in_app" => true,
                        "in_app_title" => 'Reservation',
                        "title" => 'Reservation',
                        "in_app_body" => 'You have successfully booked the ' . $custom['code'] . ' package . This reservation is only valid for one hour . ',
                        "body" => 'You have successfully booked the ' . $custom['code'] . ' package . This reservation is only valid for one hour . ',
                        "event" => 'reservation',
                        "user_id" => (int)$params["user_id"],
                        "item_id" => (int)$params['item_id'],
                        "sender_id" => 0,
                        "type" => 'info',
                        "image_url" => '',
                        "receiver_id" => (int)$params["user_id"],
                    ],
            ];
            $notificationParams['push'] = $notificationParams['information'];
            $this->notificationService->send($notificationParams, 'customer');
        }
        $params = [
            "slug" => $params["user_id"],
        ];
        return $this->getReserveList($params, $account);
    }


    /**
     * @param string $ownerSlug
     * @param object|array|null $params
     * @param $code
     * @param bool|int $expired
     * @param string $ownerTitle
     * @return array
     */
    private function getReserveParams(string $ownerSlug, object|array|null $params, $code, bool|int $expired, string $ownerTitle): array
    {
        $ownerInfo = [
            "slug" => $ownerSlug,
            "time" => date('Y / m / d H:i', time()),
            "user_id" => $params["user_id"],
            "item_id" => $params["item_id"],
            "user" => $this->accountService->getProfile(['user_id' => $params["user_id"]]),
            "item" => $this->getItem($params["item_id"], 'slug'),
            "code" => $code,
            "expired_at" => date('Y / m / d H:i', $expired),
        ];
        $ownerInfo['user']['avatar'] = 'https://cdn.seylaneh.co/general/avatar.png';

        $ownerParam = [
            "id" => null,
            "title" => $ownerTitle,
            "slug" => $ownerSlug,
            "type" => "reservation",
            "status" => 1,
            "user_id" => $params["user_id"],
            "information" => json_encode($ownerInfo),
        ];
        $ownerInfo['user']['avatar'] = 'https://cdn.seylaneh.co/general/avatar.png';
        return $ownerParam;
    }


    public function removeReserve(array $params, array $account, bool $apply = false): array
    {
        ///check code is valid
        $customList = $this->scoreService->getActiveCustomList(
            [
                "item_id" => $params["item_id"],
                "code" => $params["code"]
            ]
        );


        ///TODO: set error handler for when the code or item_id is not valid
        if (sizeof($customList) == 0) {
            return ["id" => -1];
        }
        $custom = $customList[0];

        $ownerSlug = $params["type"] . "_customer_" . $params["user_id"] . "_owner_" . $params["item_id"] . "_";
        $customerSlug = $params["type"] . "_customer_" . $params["user_id"] . "_" . $params["code"];

        $this->itemRepository->destroyItem(['slug' => $ownerSlug]);
        $this->itemRepository->destroyItem(['slug' => $customerSlug]);

        /// if this reserve removed by customer (called by remove reserve handler) - Else : this reserve removed by earn Score (called from earn score handler)
        if (!$apply) {
            $this->scoreService->updateCustom(
                [
                    'code' => $custom['code'],
                ],
                [
                    'count_used' => $custom['count_used'] - 1,
                ]
            );
        }

        return $this->getReserveList([], $account);
    }

    //// End Reservation Section /////


    public function updateItemMeta(array $params)
    {
        $item = $this->itemRepository->getItem($params['id'], 'id');
        $information = json_decode($item->getInformation(), true);
//        $information['meta'] = $nullObject;
        $information['meta'][$params['meta_key']] = $params['meta_value'];
        $editedMeta = [
            'id' => (int)$item->getId(),
            'time_update' => time(),
            'information' => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];
        $newInformationObject = json_decode($this->itemRepository->editItem($editedMeta)->getInformation(), true);
        $newInformationObject['id'] = (int)$item->getId();
        /// check that this record has a parent or no
        if (str_contains($item->getTitle(), 'child_slug_')) {
            $parent = $this->itemRepository->getItem(str_replace("child_slug_", "", $item->getTitle()), 'slug');
            $oldInformation = json_decode($parent->getInformation(), true);
            $i = 0;
            foreach ($oldInformation["body"]["answer"] as $answer) {
                if (isset($answer["id"])) {
                    if ($answer["id"] == (int)$item->getId()) {
                        $oldInformation["body"]["answer"][$i] = $newInformationObject;
                    }
                }
                $i++;
            }

            $editedParent = [
                "id" => (int)$parent->getId(),
                "time_update" => time(),
                "information" => json_encode($oldInformation, JSON_UNESCAPED_UNICODE),
            ];
            $this->itemRepository->editItem($editedParent);
        }
    }


    /// TODO: move to  independent service
    public function getReportClubScoreList(array $params, mixed $account)
    {
        $scoreList = $this->scoreService->getScoreList($params, $account);
        $list = array();
        $canonized = array();
        if (isset($scoreList['data'])) {
            if (isset($scoreList['data']['list'])) {
                foreach ($scoreList['data']['list'] as $record) {
                    $list[] = $this->canonizeReportScoreList($record);
                }
                $scoreList['data']['list'] = $list;
            }
        }
        return $scoreList;
    }

    private function canonizeReportScoreList(mixed $record)
    {
        $user = $this->accountService->getAccount(['id' => $record['user_id']]);
        $user['avatar'] = 'https://cdn.seylaneh.co/general/avatar.png';
        $item = $this->getItem($record['item_id'], 'slug');
        $record['user'] = $user;
        $record['item'] = $item;
        return $record;
    }

    private function sendNotification($notificationTypes, array $userAccount, $title)
    {
        foreach ($notificationTypes as $type) {
            if ($type == 'sms') {
                if (isset($userAccount['mobile']))
                    $this->notificationService->send(
                        [
                            'sms' => [
                                'message' => $title,
                                'mobile' => $userAccount['mobile'],
                                'source' => '',
                            ],
                        ]
                    );
            }
        }
    }

    /**
     * @param string $parameter
     * @param string $type
     *
     * @return array
     */
    public function getTour($params, $account): array
    {
        $item = $this->itemRepository->getItem($params[$params['parameter_type']], $params['parameter_type'], $params);
        return $this->canonizeItem($item);
    }

    public function getDestination($params, $account): array
    {
        $item = $this->itemRepository->getItem($params[$params['parameter_type']], $params['parameter_type'], $params);
        return $this->canonizeItem($item);
    }

    public function getTourismMainDashboard($params, $account): array
    {
        $top_sections = array();
        $new_sections = array();
        $suggest_sections = array();


        $top_sections_caller = $this->itemRepository->getItemList(
            [
                'type' => 'destination',
                'order' => ['time_create ASC', 'id ASC'],
                'offset' => 0,
                'page' => 1,
                'limit' => 6,
                'status' => 1,
            ]
        );
        foreach ($top_sections_caller as $row) {
            $top_sections[] = $this->canonizeItem($row);
        }
        $new_sections_caller = $this->itemRepository->getItemList(
            [
                'type' => 'tour',
                'order' => ['time_create DESC', 'id DESC'],
                'offset' => 0,
                'page' => 1,
                'limit' => 5,
                'status' => 1,
            ]
        );
        foreach ($new_sections_caller as $row) {
            $new_sections[] = $this->canonizeItem($row);
        }

        $suggest_sections_caller = $this->itemRepository->getItemList(
            [
                'type' => 'tour',
                'order' => ['time_create DESC', 'id DESC'],
                'offset' => 0,
                'page' => 1,
                'limit' => 4,
                'status' => 1,
            ]
        );
        foreach ($suggest_sections_caller as $row) {
            $suggest_sections[] = $this->canonizeItem($row);
        }

        $jayParsedAry = [
            "main_slider" => [
                "video" => "",
                "banner" => "https://yadapi.kerloper.com/upload/banners/home.jpg",
                "has_video" => false
            ],
            "top_section" => [
                "list" => $new_sections,
                "type" => "tour",
                "title" => "جدیدترین ها",
                "abstract" => ""
            ],
            "blog_section" => [
                "list" => $top_sections,
                "type" => "blog",
                "title" => "ایران را بهتر بشناسید",
                "abstract" => "در اینجا چند چیز وجود دارد که به شما کمک می کند تا با این کشور منحصر به فرد آشنا شوید."
            ],
            "middle_slider" => [
                "banners" => [
                    [
                        "url" => "https://yadapi.kerloper.com/upload/banners/banner-01.jpg"
                    ],
                    [
                        "url" => "https://yadapi.kerloper.com/upload/banners/banner-02.jpg"
                    ],
                    [
                        "url" => "https://yadapi.kerloper.com/upload/banners/banner-03.jpg"
                    ],
                    [
                        "url" => "https://yadapi.kerloper.com/upload/banners/banner-04.jpg"
                    ]
                ]
            ],
            "bottom_section" => [
                "list" => $suggest_sections,
                "type" => "tour",
                "title" => "مکان‌های پیشنهادی برای
سفر بعدی شما",
                "abstract" => "نمی دانید در سفر بعدی خود به کجا بروید یا چه کاری انجام دهید؟ جای نگرانی نیست ما منتخبی از سفرهای انتخاب شده از نقاط مختلف کشور را جمع آوری کرده ایم. مناظر جالب برای دیدن، مکان‌هایی برای بازدید و رستوران‌هایی برای صرف غذا پیدا کنید."
            ],
            "freq_questions" => [
                "banner" => "https://yadapi.kerloper.com/upload/faq.jpg",
                "questions" => [
                    [
                        "answer" => "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است، و برای شرایط فعلی تکنولوژی مورد نیاز، و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد، کتابهای زیادی در شصت و سه درصد گذشته حال و آینده، شناخت فراوان جامعه و متخصصان را می طلبد، تا با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی، و فرهنگ پیشرو در زبان فارسی ایجاد کرد، در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها، و شرایط سخت تایپ به پایان رسد و زمان مورد نیاز شامل حروفچینی دستاوردهای اصلی، و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.",
                        "question" => "بهترین زمان سال برای سفر به ایران؟"
                    ],
                    [
                        "answer" => "بهترین زمان سال برای سفر به شمال؟بهترین زمان سال برای سفر به شمال؟بهترین زمان سال برای سفر به شمال؟بهترین زمان سال برای سفر به شمال؟",
                        "question" => "بهترین زمان سال برای سفر به شمال؟"
                    ],
                    [
                        "answer" => "بهترین زمان سال برای سفر به کیش؟بهترین زمان سال برای سفر به کیش؟بهترین زمان سال برای سفر به کیش؟بهترین زمان سال برای سفر به کیش؟",
                        "question" => "بهترین زمان سال برای سفر به کیش؟"
                    ],
                    [
                        "answer" => "بهترین زمان سال برای سفر به قشم؟بهترین زمان سال برای سفر به قشم؟بهترین زمان سال برای سفر به قشم؟بهترین زمان سال برای سفر به قشم؟",
                        "question" => "بهترین زمان سال برای سفر به قشم؟"
                    ]
                ]
            ],
            "client_information" => [
                "weather" => "ابری",
                "location" => "تهران",
                "temperature" => "5°C",
                "weather_icon" => "https://yadapi.kerloper.com/upload/icon/cloudy.jpg"
            ],
            "bottom_slider_banner" => [
                "video" => "",
                "banner" => "https://yadapi.kerloper.com/upload/banners/banner-01.jpg",
                "has_video" => false
            ],
            "middle_slider_banner" => [
                "video" => "",
                "banner" => "https://yadapi.kerloper.com/upload/banners/banner-02.jpg",
                "has_video" => false
            ]
        ];
        return $jayParsedAry;

    }

    public function addEntity(object|array|null $request, mixed $account): array
    {

        ///TODO : handel store meta key and value in meta_value table (for filter search and ...)
        if (isset($request['meta']))
            $this->addMetaData($request);
        $request['slug'] = $request['slug']??uniqid();
        $request['time_create'] = time();
        $request['status'] = 1;
        $request['type'] = $request['type'] ?? 'entity';
        $request['user_id'] = $account['id'] ?? 0;
        $request['body'] = [];
        $params = [
            'user_id' => $request['user_id'],
            'title' => $request['title'],
            'slug' => $request['slug'],
            'status' => $request['status'],
            'type' => $request['type'],
            'time_create' => $request['time_create'],
            "information" => json_encode($request),
        ];

        $item = ($this->itemRepository->addItem($params));
        $information = $this->canonizeItem($item);
        $information['id'] = $item->getId();
        return $this->canonizeItem($this->editItem(['id' => $item->getId(), 'information' => json_encode($information)]));
    }

    public function updateEntity(object|array|null $request, mixed $account): array
    {
        ///TODO : handel store meta key and value in meta_value table (for filter search and ...)
        ///
        $this->itemRepository->destroyMetaValue(['item_slug'=>$request['slug']]);
        if (isset($request['meta']))
            $this->addMetaData($request);

        $entity = $this->getItem($request[$request['type']] ?? -1, $request['type']);
        $object = $request['body'];
        $params = [];
        if ($request['mode'] == 'body') {
            $object['id'] = time();
            $entity['body'][sizeof($entity['body'])] = $object;

            usort($entity['body'], function ($a, $b) {
                if ($a['index'] === $b['index']) {
                    return $b['id'] - $a['id'];
                }
                return $a['index'] - $b['index'];
            });
            $params = [
                'information' => json_encode($entity)
            ];
        }

        if ($request['mode'] == 'entity') {
            $request['type'] = $request['type'] ?? 'entity';
            $request['body'] = [];

            $information = $entity;
            foreach ($information as $key => $value) {
                if (isset($request[$key]) && $key != 'body') {
                    $information[$key] = $request[$key];
                }
            }

            $params = [
                'title' => $request['title'],
                'status' => $request['status'] ?? 1,
                'information' => json_encode($information),
            ];

        }
        $params[$request['type']] = $request[$request['type']];

        return $this->canonizeItem($this->editItem($params));

    }

    public function replaceEntity(mixed $request, mixed $account): array
    {
        $entity = $this->getItem($request[$request['type']] ?? -1, $request['type']);
        $object = $request['body'];
//        $entity['body'] = array_filter($entity['body'], function ($element) use ($object) {
//            return $element['id'] !== $object['id'];
//        });

        $temp = [];
        $i = 0;
        foreach ($entity['body'] as $obj) {
            if ($obj['id'] !== $object['id']) {
                $temp [$i] = $obj;
                $i++;
            }
        }
        $entity['body'] = $temp;


        $params = [
            $request['type'] => $request[$request['type']],
            'information' => json_encode($entity)
        ];
        $this->editItem($params);
        return $entity;
    }

    private function addMetaData(object|array $request): void
    {
        foreach ($request['meta'] as $meta) {
            $params = [];
            $value = $this->getItem($meta['meta_value'], 'slug');
            if (sizeof($value) > 0) {
                $params = [
                    "item_slug" => $request['slug'] ?? null,
                    "meta_key" => $meta['meta_key'] ?? null,
                    "value_slug" => $value['slug'] ?? null,
                    "value_string" => $value['title'] ?? null,
                    "value_number" => $value['number'] ?? null,
                    "value_id" => $value['id'],
                    'time_create' => time()
                ];
            } else {
                $params = [
                    "item_slug" => $request['slug'] ?? null,
                    "meta_key" => $meta['meta_key'] ?? null,
                    "value_string" => $meta['meta_value'] ?? null,
                    "value_slug" => $meta['meta_value'] ?? null,
                    "value_number" => $meta['meta_value'] ?? null,
                    'time_create' => time()
                ];
            }
            if (isset($request['id'])) {
                if ($request['id'] != null)
                    ///TODO:resolve this
                    $params["item_id"] = $request['id'];
            }
            $this->itemRepository->addMetaValue($params);
        }
    }
}
