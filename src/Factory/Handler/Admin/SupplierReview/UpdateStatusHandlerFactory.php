<?php

namespace Content\Factory\Handler\Admin\SupplierReview;

use Content\Handler\Admin\SupplierReview\UpdateStatusHandler;
use Content\Service\SupplierReviewService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class UpdateStatusHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UpdateStatusHandler
    {
        return new UpdateStatusHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(SupplierReviewService::class)
        );
    }
}
