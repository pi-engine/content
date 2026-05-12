<?php

namespace Content\Factory\Handler\Public\Workflow;

use Content\Handler\Public\Workflow\VendorsHandler;
use Content\Service\WorkflowDataService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class VendorsHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): VendorsHandler
    {
        return new VendorsHandler($container->get(WorkflowDataService::class));
    }
}
