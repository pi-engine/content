<?php

namespace Content\Factory\Service;

use Content\Repository\SupplierScoreRepository;
use Content\Repository\SupplierScoreTypeRepository;
use Content\Service\ItemService;
use Content\Service\SupplierScoreService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SupplierScoreServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SupplierScoreService
    {
        return new SupplierScoreService(
            $container->get(SupplierScoreTypeRepository::class),
            $container->get(SupplierScoreRepository::class),
            $container->get(ItemService::class)
        );
    }
}
