<?php

namespace Content\Factory\Handler\Admin\Material;

use Content\Handler\Admin\Material\GetHandler;
use Content\Service\MaterialService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class GetHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GetHandler
    {
        return new GetHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(MaterialService::class)
        );
    }
}
