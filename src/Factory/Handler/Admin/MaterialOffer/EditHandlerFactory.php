<?php

namespace Content\Factory\Handler\Admin\MaterialOffer;

use Content\Handler\Admin\MaterialOffer\EditHandler;
use Content\Service\MaterialOfferService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class EditHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EditHandler
    {
        return new EditHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(MaterialOfferService::class)
        );
    }
}
