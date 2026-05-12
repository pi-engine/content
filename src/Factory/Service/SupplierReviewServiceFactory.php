<?php

namespace Content\Factory\Service;

use Content\Repository\SupplierReviewRepository;
use Content\Service\ItemService;
use Content\Service\SupplierReviewService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Service\AccountService as UserAccountService;

class SupplierReviewServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SupplierReviewService
    {
        $accountService = $container->has(UserAccountService::class)
            ? $container->get(UserAccountService::class)
            : null;
        return new SupplierReviewService(
            $container->get(SupplierReviewRepository::class),
            $container->get(ItemService::class),
            $accountService
        );
    }
}
