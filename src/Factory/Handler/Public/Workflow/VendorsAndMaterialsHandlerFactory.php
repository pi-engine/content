<?php

namespace Content\Factory\Handler\Public\Workflow;

use Content\Handler\Public\Workflow\VendorsAndMaterialsHandler;
use Content\Service\WorkflowDataService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class VendorsAndMaterialsHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): VendorsAndMaterialsHandler
    {
        return new VendorsAndMaterialsHandler($container->get(WorkflowDataService::class));
    }
}
