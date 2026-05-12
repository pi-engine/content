<?php

namespace Content\Factory\Handler\User\MaterialOffer;

use Content\Handler\User\MaterialOffer\ListHandler;
use Content\Service\MaterialOfferService;
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
            $container->get(MaterialOfferService::class)
        );
    }
}
