<?php

namespace Content\Factory\Repository;

use Content\Repository\SupplierScoreTypeRepository;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SupplierScoreTypeRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SupplierScoreTypeRepository
    {
        return new SupplierScoreTypeRepository(
            $container->get(AdapterInterface::class)
        );
    }
}
