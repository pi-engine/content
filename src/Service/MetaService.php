<?php

namespace Content\Service;

use Content\Repository\ItemRepositoryInterface;
use mysql_xdevapi\Exception;
use User\Service\AccountService;
use function array_diff_key;
use function array_flip;
use function array_merge;
use function explode;
use function in_array;
use function is_array;
use function is_object;
use function json_decode;
use function json_encode;
use function strtolower;
use function uniqid;

class MetaService implements ServiceInterface
{


    /** @var AccountService */
    protected AccountService $accountService;

    /** @var ItemService */
    protected ItemService $itemService;

    /** @var LogService */
    protected LogService $logService;

    /* @var ItemRepositoryInterface */
    protected ItemRepositoryInterface $itemRepository;
    protected array $allowKey
        = [
            'type', 'category', 'brand', 'min_price', 'max_price', 'title', 'color', 'size',
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
        ItemService             $itemService,
        LogService              $logService,
                                $config
    )
    {
        $this->itemRepository = $itemRepository;
        $this->accountService = $accountService;
        $this->itemService = $itemService;
        $this->logService = $logService;
        $this->config = $config;
    }


    public function canonizeMeta(object|array $meta): array
    {
        if (empty($meta)) {
            return [];
        }

        if (is_object($meta)) {
            $meta = [
                'id' => $meta->getId(),
                'item_id' => $meta->getItemId(),
                'meta_key' => $meta->getMetaKey(),
                'value_string' => $meta->getValueString(),
                'value_id' => $meta->getValueId(),
                'value_number' => $meta->getValueNumber(),
                'status' => $meta->getStatus(),
                'logo' => $meta->getLogo(),
                'time_create' => $meta->getTimeCreate(),
                'time_update' => $meta->getTimeUpdate(),
                'time_delete' => $meta->getTimeDelete(),

            ];
        } else {
            $meta = [
                'id' => $meta['id'],
                'item_id' => $meta['item_id'],
                'meta_key' => $meta['meta_key'],
                'value_string' => $meta['value_string'],
                'value_id' => $meta['value_id'],
                'value_number' => $meta['value_number'],
                'status' => $meta['status'],
                'logo' => $meta['logo'],
                'time_create' => $meta['time_create'],
                'time_update' => $meta['time_update'],
                'time_delete' => $meta['time_delete'],
            ];
        }

        return $meta;
    }

    ///// Start Opinion Section /////
    ///
    public function opinion(object|array $requestBody, array $log): array
    {

        ///check that user like this item  in before
        $hasLike = !empty($this->logService->getLog(
            [
                "user_id" => $log["user_id"],
                "item_id" => $log["item_id"],
                "action" => "like",
                "time_delete" => 0,
            ]
        ));

        ///check that user dislike this item  in before
        $hasDislike = !empty($this->logService->getLog(
            [
                "user_id" => $log["user_id"],
                "item_id" => $log["item_id"],
                "action" => "dislike",
                "time_delete" => 0,
            ]
        ));

        $metaPrams = [
            "item_id" => $requestBody["item_id"],
            "meta_key" => $requestBody["action"],
        ];


        $row = $this->itemRepository->getMetaValue($metaPrams, "object");
        $currentMeta = $this->canonizeMeta($row);

        $isFirstOpinion = empty($currentMeta);

        /// add first opinion for item
        if ($isFirstOpinion) {
            $metaPrams["value_number"] = 1;
            $currentMeta = $this->addMetaValue($metaPrams);
            $this->logService->addLog($log);

        }

        if ($requestBody["action"] == "like") {
            if ($hasLike)
                return $currentMeta;
            if ($hasDislike) {
                $this->minusDislike($metaPrams, $log);
            }
        }

        if ($requestBody["action"] == "dislike") {

            if ($hasDislike)
                return $currentMeta;

            if ($hasLike) {
                $this->minusLike($metaPrams, $log);
            }
        }

        if (!$isFirstOpinion) {
            $metaPrams["value_number"] = $currentMeta["value_number"] + 1;
            $metaPrams["id"] = $currentMeta["id"];
            $currentMeta = $this->updateMeta($metaPrams);
            $this->logService->addLog($log);
        }

        ///TODO: complete this
        $this->itemService->updateItemMeta(
            [
                "id" => $requestBody["item_id"],
                "meta_key" => $requestBody["action"],
                "meta_value" => $currentMeta["value_number"]
            ]
        );
        return $currentMeta;

    }

    public function addMetaValue(array $metaPrams): array
    {
        $row = $this->itemRepository->addMetaValue($metaPrams);
        return $this->canonizeMeta($row);
    }

    public function updateMeta(array $metaPrams): array
    {
        $row = $this->itemRepository->addMetaItem($metaPrams);
        return $this->canonizeMeta($row);
    }

    private function minusDislike(array $metaPrams, array $log): void
    {
        $log["time_delete"] = time();
        $log["action"] = "dislike";
        $prams = [
            "item_id" => $metaPrams["item_id"],
            "meta_key" => "dislike",
        ];

        $this->updateOpinion($prams, $log);
    }

    private function minusLike(array $metaPrams, array $log): void
    {
        $log["time_delete"] = time();
        $log["action"] = "like";
        $prams = [
            "item_id" => $metaPrams["item_id"],
            "meta_key" => "like",
        ];

        $this->updateOpinion($prams, $log);
    }

    /**
     * @param array $prams
     * @param array $log
     * @return void
     */
    private function updateOpinion(array $prams, array $log): void
    {
        $row = $this->itemRepository->getMetaValue($prams, "object");
        $currentMeta = $this->canonizeMeta($row);
        if (!empty($currentMeta)) {
            $prams["value_number"] = $currentMeta["value_number"] > 0 ? $currentMeta["value_number"] - 1 : 0;
            $prams["id"] = $currentMeta["id"];
            $this->itemRepository->updateMetaValue($prams);
            $this->itemService->updateItemMeta(
                [
                    "id" => $prams["item_id"],
                    "meta_key" => $prams["meta_key"],
                    "meta_value" => $prams["value_number"]
                ]
            );
        }
        $this->logService->updateLog($log);
    }

    public function getMetaKeyList(object|array|null $params): array
    {
        $limit = $params['limit'] ?? 125;
        $page = $params['page'] ?? 1;
        $order = $params['order'] ?? ['id DESC'];
        $offset = ($page - 1) * $limit;
        $params['type'] = $params['type'] ?? '';

        // Set params
        $listParams = [
            'order' => $order,
            'offset' => $offset,
            'limit' => $limit,
            'type' => $params['type'],
            'status' => 1,
        ];
        if (isset($params['target']) && !empty($params['target'])) {
            $listParams['target'] = $params['target'];
        }

        $rowSet = $this->itemRepository->getMetaKeyList($listParams);
        $list = [];
        foreach ($rowSet as $row) {
            $list[] = $this->canonizeMetaKey($row, $params['type']);
        }

        // Get count
        $count = $this->itemRepository->getMetaKeyCount($listParams);

        return [
            'result' => true,
            'data' => [
                'list' => $list,
                'paginator' => [
                    'count' => $count,
                    'limit' => $limit,
                    'page' => $page,
                ],
                'filters' => [],
            ],
            'error' => [],
        ];


    }

    public function getMetaKey(object|array|null $params): object|array
    {
        return $this->canonizeMetaKey($this->itemRepository->getMetaKey($params));
    }


    public function getMetaValueList(object|array|null $params): array
    {
        $params['key'] = $params['key'] ?? '';
        if (is_array($params['key'])) {
            $params['type'] = $params['key'];
        } else {
            $params['type'] = 'meta-' . $params['key'] ?? '';
        }
        return $this->itemService->getItemList($params);
    }

    /**
     * Return industry and sub_industry lists as a single JSON-friendly array.
     * Uses parent_id: industries have parent_id=0, sub-industries have parent_id=industry id.
     *
     * @return array{result: bool, data: array{industry: array, sub_industry: array}, error: array}
     */
    public function getIndustrySubIndustryList(): array
    {
        $all = $this->itemService->getItemList([
            'type' => 'meta-industry',
            'status' => 1,
            'limit' => 500,
        ]);
        $list = $all['data']['list'] ?? [];
        $industry = [];
        $subIndustry = [];
        foreach ($list as $row) {
            $parentId = (int) ($row['parent_id'] ?? 0);
            if ($parentId === 0) {
                $industry[] = $row;
            } else {
                $subIndustry[] = $row;
            }
        }
        return [
            'result' => true,
            'data' => [
                'industry' => $industry,
                'sub_industry' => $subIndustry,
            ],
            'error' => [],
        ];
    }

    public function createMetaValue(object|array|null $requestBody, array $account = []): array
    {
        if (is_object($requestBody)) {
            $requestBody = (array)$requestBody;
        }

        if (!is_array($requestBody)) {
            $requestBody = [];
        }

//        $type = $requestBody['type'] ?? null;
//        if ($type === null && isset($requestBody['key']) && !is_array($requestBody['key'])) {
            $type = sprintf('meta-%s', $requestBody['key']);
//        }

        if (empty($type)) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => [
                    'type' => 'Meta type is required. Provide `type` or `key` in request body.',
                ],
            ];
        }

        if (empty($requestBody['title'])) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => [
                    'title' => 'Title is required.',
                ],
            ];
        }

        $request['type'] = $type;
        $request['user_id'] = $requestBody['user_id'] ?? ($account['id'] ?? 0);
        $request['title'] = $requestBody['title'] ;
        $request['status'] = $requestBody['status'] ?? 1;
        $request['time_create'] = $requestBody['time_create'] ?? time();
        $request['priority'] = $requestBody['priority'] ?? 0;
        $request['slug'] = $requestBody['slug']  ;

        if (empty($requestBody['slug'])) {
            $request['slug'] = strtolower($type . '-' . uniqid());
        }

        $information = [];
        if (isset($requestBody['information'])) {
            if (is_array($requestBody['information']) || is_object($requestBody['information'])) {
                $information = (array)$requestBody['information'];
            }
        }

        $columnKeys = [
            'title',
            'slug',
            'priority',
            'type',
            'status',
            'user_id',
            'time_create',
            'time_update',
            'time_delete',
            'information',
        ];

        $extraPayload = array_diff_key($requestBody, array_flip($columnKeys));
        if (!empty($extraPayload)) {
            $information = array_merge($information, $extraPayload);
        }

        $information['title'] = $requestBody['title'];
        $information['slug'] = $requestBody['slug'];
        $information['priority'] = $requestBody['priority'];

        $request['information'] = json_encode($information, JSON_UNESCAPED_UNICODE);

        $created = $this->itemService->addItem($request, $account);

        return [
            'result' => true,
            'data'   => $created,
            'error'  => [],
        ];
    }

    private function canonizeMetaKey(mixed $meta, mixed $type = 'global'): array
    {
        if (empty($meta)) {
            return [];
        }

        if (is_object($meta)) {
            $meta = [
                'id' => $meta->getId(),
                'key' => $meta->getKey(),
                'value' => $meta->getValue(),
                'type' => $meta->getType(),
                'suffix' => $meta->getSuffix(),
                'option' => json_decode($meta->getOption()),
                'logo' => $meta->getLogo(),
                'status' => $meta->getStatus(),

            ];
        } else {
            $meta = [
                'id' => $meta['id'],
                'key' => $meta['key'],
                'value' => $meta['value'],
                'type' => $meta['type'],
                'suffix' => $meta['suffix'],
                'option' => json_decode($meta['option']),
                'logo' => $meta['logo'],
                'status' => $meta['status'],
            ];
        }

        return $meta;

    }

    public function addMetaKey(object|array|null $requestBody): array
    {
        $requestBody['option'] = json_encode($requestBody['option']);
        $meta = $this->itemRepository->addMetaKey($requestBody);
        return [
          "result" => true,
          "data"=>true,
          "error"=> null
        ];
    }
    public function updateMetaKey(object|array|null $requestBody): array
    {
        $requestBody['option'] = json_encode($requestBody['option']);
        $requestBody['id'] = $requestBody['id']??-1;
        $meta = $this->itemRepository->updateMetaKey($requestBody);
        return [
          "result" => true,
          "data"=>true,
          "error"=> null
        ];
    }

}
