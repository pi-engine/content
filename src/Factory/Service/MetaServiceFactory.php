<?php

namespace Content\Factory\Service;

use Content\Repository\ItemRepositoryInterface;
use Content\Service\ItemService;
use Content\Service\LogService;
use Content\Service\MetaService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Service\AccountService;

class MetaServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return MetaService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MetaService
    {
        $config = $container->get('config');

        return new MetaService(
            $container->get(ItemRepositoryInterface::class),
            $container->get(AccountService::class),
            $container->get(ItemService::class),
            $container->get(LogService::class),
            $config['client']
        );
    }
}
