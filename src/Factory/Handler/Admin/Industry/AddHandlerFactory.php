<?php

namespace Content\Factory\Handler\Admin\Industry;

use Content\Handler\Admin\Industry\AddHandler;
use Content\Service\IndustryService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class AddHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AddHandler
    {
        return new AddHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(IndustryService::class)
        );
    }
}
