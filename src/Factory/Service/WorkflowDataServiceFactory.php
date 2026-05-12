<?php

namespace Content\Factory\Service;

use Content\Service\MaterialOfferService;
use Content\Service\MaterialService;
use Content\Service\MetaService;
use Content\Service\SupplierService;
use Content\Service\WorkflowDataService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class WorkflowDataServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): WorkflowDataService
    {
        return new WorkflowDataService(
            $container->get(MetaService::class),
            $container->get(MaterialService::class),
            $container->get(MaterialOfferService::class),
            $container->get(SupplierService::class)
        );
    }
}
