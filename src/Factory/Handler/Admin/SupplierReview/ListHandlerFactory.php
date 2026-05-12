<?php

namespace Content\Factory\Handler\Admin\SupplierReview;

use Content\Handler\Admin\SupplierReview\ListHandler;
use Content\Service\SupplierReviewService;
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
            $container->get(SupplierReviewService::class)
        );
    }
}
