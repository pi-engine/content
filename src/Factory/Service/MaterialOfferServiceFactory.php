<?php

namespace Content\Factory\Service;

use Content\Service\ItemService;
use Content\Service\MaterialOfferService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MaterialOfferServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MaterialOfferService
    {
        return new MaterialOfferService($container->get(ItemService::class));
    }
}
