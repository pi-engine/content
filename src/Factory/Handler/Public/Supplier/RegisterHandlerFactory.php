<?php

namespace Content\Factory\Handler\Public\Supplier;

use Content\Handler\Public\Supplier\RegisterHandler;
use Content\Service\SupplierService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class RegisterHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RegisterHandler
    {
        return new RegisterHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(SupplierService::class)
        );
    }
}
