<?php

namespace Content\Factory\Handler\Api\Supplier;

use Content\Handler\Api\Supplier\UpdateHandler;
use Content\Service\SupplierService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use User\Service\AccountService;

class UpdateHandlerFactory
{
    public function __invoke(ContainerInterface $container): UpdateHandler
    {
        return new UpdateHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(SupplierService::class),
            $container->get(AccountService::class)
        );
    }
}
