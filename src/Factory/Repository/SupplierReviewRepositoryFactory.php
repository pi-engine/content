<?php

namespace Content\Factory\Repository;

use Content\Repository\SupplierReviewRepository;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SupplierReviewRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SupplierReviewRepository
    {
        return new SupplierReviewRepository(
            $container->get(AdapterInterface::class)
        );
    }
}
