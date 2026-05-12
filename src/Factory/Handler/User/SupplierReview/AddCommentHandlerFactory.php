<?php

namespace Content\Factory\Handler\User\SupplierReview;

use Content\Handler\User\SupplierReview\AddCommentHandler;
use Content\Service\SupplierReviewService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class AddCommentHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AddCommentHandler
    {
        return new AddCommentHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(SupplierReviewService::class)
        );
    }
}
