<?php

namespace Content\Factory\Service;

use Club\Service\ScoreService;
use Content\Repository\ItemRepositoryInterface;
use Content\Service\LogService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Service\AccountService;

class LogServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return LogService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LogService
    {
        $config = $container->get('config');

        return new LogService(
            $container->get(AccountService::class),
            $container->get(ItemRepositoryInterface::class),
            $config['log']
        );
    }
}
