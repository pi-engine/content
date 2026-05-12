<?php

namespace Content\Factory\Handler\Admin\MaterialOffer;

use Content\Handler\Admin\MaterialOffer\PricingContextHandler;
use Content\Service\MaterialOfferService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PricingContextHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PricingContextHandler
    {
        $config   = $container->get('config');
        $baseUrl  = $config['ai_core']['base_url'] ?? 'http://127.0.0.1:3333';
        return new PricingContextHandler(
            $container->get(MaterialOfferService::class),
            $baseUrl
        );
    }
}
