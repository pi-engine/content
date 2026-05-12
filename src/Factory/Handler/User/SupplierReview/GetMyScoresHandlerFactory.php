<?php

namespace Content\Factory\Handler\User\SupplierReview;

use Content\Handler\User\SupplierReview\GetMyScoresHandler;
use Content\Service\SupplierScoreService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class GetMyScoresHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GetMyScoresHandler
    {
        return new GetMyScoresHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(SupplierScoreService::class)
        );
    }
}
