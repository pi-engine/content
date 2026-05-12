<?php

namespace Content\Factory\Handler\Public\Workflow;

use Content\Handler\Public\Workflow\IndustriesMaterialsHandler;
use Content\Service\WorkflowDataService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndustriesMaterialsHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IndustriesMaterialsHandler
    {
        return new IndustriesMaterialsHandler($container->get(WorkflowDataService::class));
    }
}
