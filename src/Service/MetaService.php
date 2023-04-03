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


    ///// Start Opinion Section /////
    ///
    public function like(object|array $requestBody, array $log)
    {
        return 1;
    }

    public function Dislike(object|array $requestBody, $log = null)
    {
        return 0;
    }
    //// End Reservation Section /////

}
