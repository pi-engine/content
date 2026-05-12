<?php

namespace Content\Factory\Handler\Admin\Material;

use Content\Handler\Admin\Material\EditHandler;
use Content\Service\MaterialService;
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
            $container->get(MaterialService::class)
        );
    }
}
