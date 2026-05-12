<?php

namespace Content\Factory\Service;

use Content\Service\ItemService;
use Content\Service\MaterialService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MaterialServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MaterialService
    {
        return new MaterialService($container->get(ItemService::class));
    }
}
