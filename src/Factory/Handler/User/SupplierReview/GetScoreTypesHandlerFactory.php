<?php

namespace Content\Factory\Handler\User\SupplierReview;

use Content\Handler\User\SupplierReview\GetScoreTypesHandler;
use Content\Service\SupplierScoreService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class GetScoreTypesHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GetScoreTypesHandler
    {
        return new GetScoreTypesHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(SupplierScoreService::class)
        );
    }
}
