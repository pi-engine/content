<?php

namespace Content\Service;

use Content\Repository\ItemRepositoryInterface;
use mysql_xdevapi\Exception;
use User\Service\AccountService;
use function explode;
use function in_array;
use function is_object;
use function json_decode;

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

        return $currentMeta;

    }

    public function addMetaValue(array $metaPrams): array
    {
        $row = $this->itemRepository->addMetaValue($metaPrams);
        return $this->canonizeMeta($row);
    }

    public function updateMeta(array $metaPrams): array
    {
        $row = $this->itemRepository->updateMetaValue($metaPrams);
        return $this->canonizeMeta($row);
    }

    private function minusDislike(array $metaPrams, array $log)
    {
        $log["time_delete"] = time();
        $log["action"] = "dislike";
        $prams = [
            "item_id" => $metaPrams["item_id"],
            "meta_key" => "dislike",
        ];

        $this->extracted($prams, $log);
    }

    private function minusLike(array $metaPrams, array $log)
    {
        $log["time_delete"] = time();
        $log["action"] = "like";
        $prams = [
            "item_id" => $metaPrams["item_id"],
            "meta_key" => "like",
        ];

        $this->extracted($prams, $log);
    }

    /**
     * @param array $prams
     * @param array $log
     * @return void
     */
    private function extracted(array $prams, array $log): void
    {
        $row = $this->itemRepository->getMetaValue($prams, "object");
        $currentMeta = $this->canonizeMeta($row);
        if (!empty($currentMeta)) {
            $prams["value_number"] = $currentMeta["value_number"] > 0 ? $currentMeta["value_number"] - 1 : 0;
            $prams["id"] = $currentMeta["id"];
            $this->itemRepository->updateMetaValue($prams);
        }
        $this->logService->updateLog($log);
    }


}
