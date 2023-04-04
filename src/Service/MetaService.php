<?php

namespace Content\Service;

use Club\Service\ScoreService;
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

    /** @var ScoreService */
    protected ScoreService $scoreService;

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
        ScoreService            $scoreService,
        LogService              $logService,
                                $config
    )
    {
        $this->itemRepository = $itemRepository;
        $this->accountService = $accountService;
        $this->scoreService = $scoreService;
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
    public function like(object|array $requestBody, array $log): array
    {
        $requestBody["meta_key"] = "like";
        $requestBody["value_number"] = 1;


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
            "meta_key" => "like",
        ];


        $row = $this->itemRepository->getMetaValue($metaPrams, "object");
        $currentMeta = $this->canonizeMeta($row);

        /// add first like for item
        if (empty($currentMeta)) {
            $metaPrams["value_number"] = 1;
            $result = $this->addLike($metaPrams);
            $this->logService->addLog($log);
            return $result;
        }

        if ($hasLike)
            return $currentMeta;

        if ($hasDislike){
            $this->minusDislike($metaPrams);
        }


        $metaPrams["value_number"] = $currentMeta["value_number"]+1;
        $result = $this->updateLike($metaPrams);
        $this->logService->addLog($log);
        return $result;

        return [
            "like" => $hasLike,
            "dislike" => $hasDislike,
        ];


//        if (!empty($log)) {
//            $this->logService->addLog($log);
//        }

        return [];

    }

    public function Dislike(object|array $requestBody, $log = null)
    {
        return 0;
    }

    public function addLike(array $metaPrams)
    {
        $row = $this->itemRepository->addMetaValue($metaPrams);
        return $this->canonizeMeta($row);
    }
    public function updateLike(array $metaPrams)
    {
        $row = $this->itemRepository->updateMetaValue($metaPrams);
        return $this->canonizeMeta($row);
    }

    private function minusDislike(array $metaPrams)
    {
    }

}
