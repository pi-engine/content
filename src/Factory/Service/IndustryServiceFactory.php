<?php

namespace Content\Factory\Service;

use Content\Service\IndustryService;
use Content\Service\ItemService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndustryServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IndustryService
    {
        return new IndustryService($container->get(ItemService::class));
    }
}
