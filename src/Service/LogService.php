<?php

namespace Content\Service;

use Club\Service\ScoreService;
use Content\Repository\LogRepositoryInterface;
use mysql_xdevapi\Exception;
use User\Service\AccountService;
use function explode;
use function in_array;
use function is_object;
use function json_decode;

class LogService implements ServiceInterface
{


    /** @var AccountService */
    protected AccountService $accountService;


    /* @var LogRepositoryInterface */
    protected LogRepositoryInterface $logRepository;

    /* @var array */
    protected array $log;


    public function __construct(
        LogRepositoryInterface $logRepository,
        AccountService         $accountService,
                               $log
    )
    {
        $this->accountService = $accountService;
        $this->logRepository = $logRepository;
        $this->log = $log;
    }


    /**
     * @param null
     *
     * @return null
     */
    public function writeTestLog()
    {
//        $this->log['logger']->info('Informational message');
//        $this->log['logger']->emerg('Informational message');
        $this->logRepository->addLog(["user_id" => 5]);
    }

//    /**
//     * @param object|array $Log
//     *
//     * @return array
//     */
//    public function canonizeLog(object|array $Log): array
//    {
//        if (empty($Log)) {
//            return [];
//        }
//
//        if (is_object($Log)) {
//            $Log = [
//                'id' => $Log->getId(),
//                'title' => $Log->getTitle(),
//                'slug' => $Log->getSlug(),
//                'type' => $Log->getType(),
//                'status' => $Log->getStatus(),
//                'user_id' => $Log->getUserId(),
//                'time_create' => $Log->getTimeCreate(),
//                'time_update' => $Log->getTimeUpdate(),
//                'time_delete' => $Log->getTimeDelete(),
//                'information' => $Log->getInformation(),
//            ];
//        } else {
//            $Log = [
//                'id' => $Log['id'],
//                'title' => $Log['title'],
//                'slug' => $Log['slug'],
//                'type' => $Log['type'],
//                'status' => $Log['status'],
//                'user_id' => $Log['user_id'],
//                'time_create' => $Log['time_create'],
//                'time_update' => $Log['time_update'],
//                'time_delete' => $Log['time_delete'],
//                'information' => $Log['information'],
//            ];
//        }
//
//        // Set information
//        return !empty($Log['information']) ? json_decode($Log['information'], true) : [];
//    }


}
