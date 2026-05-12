<?php

namespace Content\Factory\Handler\Public\Meta\Value;

use Content\Handler\Public\Meta\Value\IndustrySubIndustryListHandler;
use Content\Service\MetaService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class IndustrySubIndustryListHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IndustrySubIndustryListHandler
    {
        return new IndustrySubIndustryListHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $container->get(MetaService::class)
        );
    }
}
