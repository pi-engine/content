<?php

namespace Content\Factory\Handler\User\Supplier;

use Content\Handler\User\Supplier\ListHandler;
use Content\Service\SupplierService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ListHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ListHandler
    {
        return new ListHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(SupplierService::class)
        );
    }
}
