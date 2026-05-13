<?php

namespace Content\Factory\Handler\Api\Supplier;

use Content\Handler\Api\Supplier\GetHandler;
use Content\Service\SupplierService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class GetHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetHandler
    {
        return new GetHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(SupplierService::class)
        );
    }
}
