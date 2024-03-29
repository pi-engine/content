<?php

namespace Content\Factory\Service;

use Club\Service\ScoreService;
use Content\Repository\ItemRepositoryInterface;
use Content\Service\ItemService;
use Content\Service\LogService;
use Content\Service\MetaService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Notification\Service\NotificationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Service\AccountService;
use User\Service\UtilityService;

class ItemServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return ItemService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ItemService
    {
        $config = $container->get('config');

        return new ItemService(
            $container->get(ItemRepositoryInterface::class),
            $container->get(AccountService::class),
            $container->get(ScoreService::class),
            $container->get(NotificationService::class),
            $container->get(LogService::class),
            $container->get(UtilityService::class),
            $config['client']
        );
    }
}
