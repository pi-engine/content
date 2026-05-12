<?php

namespace Content\Factory\Repository;

use Content\Repository\SupplierScoreRepository;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SupplierScoreRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SupplierScoreRepository
    {
        return new SupplierScoreRepository(
            $container->get(AdapterInterface::class)
        );
    }
}
