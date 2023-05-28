<?php

namespace Content\Service;

use Club\Service\ScoreService;
use Content\Repository\ItemRepositoryInterface;
use mysql_xdevapi\Exception;
use Notification\Service\NotificationService;
use User\Service\AccountService;

use function explode;
use function in_array;
use function is_object;
use function json_decode;

class ItemService implements ServiceInterface
{


    /** @var AccountService */
    protected AccountService $accountService;

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
                                $config
    )
    {
        $this->itemRepository = $itemRepository;
        $this->accountService = $accountService;
        $this->scoreService = $scoreService;
        $this->notificationService = $notificationService;
        $this->logService = $logService;
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
            'data_from' => strtotime(
                isset($params['data_from'])
                    ? sprintf('%s 00:00:00', $params['data_from'])
                    : sprintf('%s 00:00:00', date('Y-m-d', strtotime('-1 month')))
            ),
            'data_to' => strtotime(
                isset($params['data_to'])
                    ? sprintf('%s 23:59:59', $params['data_to'])
                    : sprintf('%s 23:59:59', date('Y-m-d'))
            ),
        ];

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

        // Set filtered IDs to params
//        if (!empty($itemIdList)) {
//            $listParams['id'] = $itemIdList;
//        }

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
            $list[] = $this->canonizeItem($row);
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
    public function getItem(string $parameter, string $type = 'id', $params = []): array
    {
        $item = $this->itemRepository->getItem($parameter, $type, $params);
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

                    case 'brand':
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

            $this->itemRepository->deleteCart(["type" => "cart", "user_id" => $params["user_id"]]);
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

        $this->itemRepository->deleteCart(["type" => "cart", "user_id" => $params["user_id"]]);
        $this->itemRepository->addCartItem($param);
    }

    // ToDo: update it
    public function deleteCartItem($params, $account)
    {
        $product = $this->getItem($params["slug"], "slug");
        $cart = $this->getItemFilter(["type" => "cart", "user_id" => $account["id"]]);
        echo sizeof($cart);
        if (sizeof($cart) < 2) {
            $this->itemRepository->deleteCart(["type" => "cart", "user_id" => $account["id"]]);
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

            $this->itemRepository->deleteCart(["type" => "cart", "user_id" => $account["id"]]);
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
    public function addOrderItem($params, $account)
    {
        $address = [];
        if (isset($params["address_id"]) && ($params["address_id"] != null) && ($params["address_id"] != "null")) {
            ///TODO:get address by address_id from db
            $address = [];
        } else {
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
            $cart = [];
            $cart["items"] = $this->getCart($cart_request);
            $cart["address"] = ($address);

            $order_information = [
                "user_id" => $account["id"],
                "status" => "created",
                "date_time" => date('m/d/Y h:i', time()),
                "description" => $params["description"],
                "items" => ($cart),
            ];

            $order_request = [
                "type" => "order",
                "slug" => "order-{$account["id"]}-" . time(),
                "user_id" => $account["id"],
                "status" => 1,
                "title" => "order-{$account["id"]}",
                "information" => json_encode($order_information),
            ];

            $this->itemRepository->deleteCart(["type" => "cart", "user_id" => $account["id"]]);
            return $this->addItem($order_request, $account);
        }
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

    public function replySupport($params): object|array
    {
        $question = $this->itemRepository->getItem(str_replace("child_slug_", "", $params["support_slug"]), "slug", $params);
        if ($question == null) {
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
        $answerInformation['user'] = $params['user_id'] == 0 ? $nullObject : $this->accountService->getProfile($params);;
        $answerInformation['name'] = $params['user_id'] == 0 ? ''
            : $this->accountService->getProfile($params)["first_name"] . ' ' . $this->accountService->getProfile($params)["last_name"];
        $answerInformation['title'] = $params['title'];
        $information['meta']['categories'] = $hasCategories ? $this->canonizeItem($this->itemRepository->getItem($params['categories'], 'id')) : [];
        $answerInformation['meta']['like'] = 0;
        $answerInformation['meta']['dislike'] = 0;

        $answer["information"] = json_encode($answerInformation, JSON_UNESCAPED_UNICODE);
        $answer = $this->itemRepository->addItem($answer);


        $params["user"] = $params["user_id"] == 0 ? $nullObject : $this->accountService->getProfile($params);


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
            $slug = "reservation_owner_" . $this->accountService->getProfile(["user_id" => $account["id"]])["item_id"];
        } else {
            $slug = "reservation_customer_" . $account["id"];
        }


        // Set params
        $listParams = [
            'order' => $order,
            'offset' => $offset,
            'limit' => $limit,
            'type' => $params['type'] ?? 'reservation',
            'status' => 1,
            'slug' => $slug,
        ];

        // Get list
        $list = [];
        $rowSet = $this->itemRepository->getItemList($listParams);
        foreach ($rowSet as $row) {
            $list = $this->canonizeItem($row);
        }

        return $list;
    }


    public function reserve(object|array|null $params, $account): array
    {
        $flag = true;
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

        $customerSlug = $params["type"] . "_customer_" . $params["user_id"];
        $ownerSlug = $params["type"] . "_owner_" . $params["item_id"];

        $customerReserve = $this->itemRepository->getItem($customerSlug, "slug");
        $ownerReserve = $this->itemRepository->getItem($ownerSlug, "slug");

        $expired = strtotime("+1 hour");

        $customerNewReserve = [
            "slug" => $customerSlug,
            "time" => date('Y/m/d H:i', time()),
            "user_id" => $params["user_id"],
            "item_id" => $params["item_id"],
            "user" => $this->accountService->getProfile(['user_id' => $params["user_id"]]),
            "item" => $this->getItem($params["item_id"], 'slug'),
            "code" => $custom["code"],
            "expired_at" => date('Y/m/d H:i', $expired),
        ];

        $ownerNewReserve = [
            "slug" => $ownerSlug,
            "time" => date('Y/m/d H:i', time()),
            "user_id" => $params["user_id"],
            "item_id" => $params["item_id"],
            "user" => $this->accountService->getProfile(['user_id' => $params["user_id"]]),
            "item" => $this->getItem($params["item_id"], 'slug'),
            "code" => $custom["code"],
            "expired_at" => date('Y/m/d H:i', $expired),
        ];

        if (empty($customerReserve)) {
            $list[] = $customerNewReserve;
            $param = [
                "id" => null,
                "title" => $customerSlug,
                "slug" => $customerSlug,
                "type" => "reservation",
                "status" => 1,
                "user_id" => $params["user_id"],
                "information" => json_encode($list),
            ];
            $item = $this->canonizeItem($this->itemRepository->addItem($param));
            $this->scoreService->updateCustom(
                [
                    'id' => $custom['id'],
                ],
                [
                    'count_used' => $custom['count_used'] + 1,
                ]
            );
        } else {
            $list = $this->canonizeItem($customerReserve);
            foreach ($list as $object) {
                if ($object["item_id"] == $params["item_id"]) {
                    $flag = false;
                }
            }
            if ($flag) {
                $list[] = $customerNewReserve;
                $param = [
                    "title" => $customerSlug,
                    "slug" => $customerSlug,
                    "type" => "reservation",
                    "status" => 1,
                    "user_id" => $params["user_id"],
                    "information" => json_encode($list),
                ];
                $item = $this->canonizeItem($this->editItem($param));
                $this->scoreService->updateCustom(
                    [
                        'id' => $custom['id'],
                    ],
                    [
                        'count_used' => $custom['count_used'] + 1,
                    ]
                );
            }
        }


        $reserveResult = $list;
        $list = [];


        if (empty($ownerReserve)) {
            $list[] = $ownerNewReserve;
            $param = [
                "id" => null,
                "title" => $ownerSlug,
                "slug" => $ownerSlug,
                "type" => "reservation",
                "status" => 1,
                "user_id" => $params["user_id"],
                "information" => json_encode($list),
            ];
            $this->canonizeItem($this->itemRepository->addItem($param));
        } else {
            $list = $this->canonizeItem($ownerReserve);
            $flag = true;
            foreach ($list as $object) {
                if ($object["user_id"] == $params["user_id"]) {
                    $flag = false;
                }
            }
            if ($flag) {
                $list[] = $ownerNewReserve;
                $param = [
                    "title" => $ownerSlug,
                    "slug" => $ownerSlug,
                    "type" => "reservation",
                    "status" => 1,
                    "user_id" => $params["user_id"],
                    "information" => json_encode($list),
                ];
                $this->canonizeItem($this->editItem($param));
            }
        }

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
                        "in_app_body" => 'You have been reserved by a user. package code is ' . $custom['code'] . ' ',
                        "body" => 'You have been reserved by a user. package code is ' . $custom['code'] . ' ',
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
                    "in_app_body" => 'You have successfully booked the ' . $custom['code'] . ' package. This reservation is only valid for one hour.',
                    "body" => 'You have successfully booked the ' . $custom['code'] . ' package. This reservation is only valid for one hour.',
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

        return $reserveResult;
    }


    public function removeReserve(array $params)
    {
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


}
