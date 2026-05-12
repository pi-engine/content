<?php

namespace Content\Factory\Service;

use Content\Service\ItemService;
use Content\Service\SupplierReviewService;
use Content\Service\SupplierScoreService;
use Content\Service\SupplierService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Service\AccountService as UserAccountService;
use User\Service\RoleService as UserRoleService;

class SupplierServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SupplierService
    {
        $reviewService = null;
        $scoreService = null;
        try {
            $reviewService = $container->get(SupplierReviewService::class);
        } catch (\Throwable $e) {
            // Optional: reviews feature may not be installed
        }
        try {
            $scoreService = $container->get(SupplierScoreService::class);
        } catch (\Throwable $e) {
            // Optional: score tables may not be installed
        }
        return new SupplierService(
            $container->get(ItemService::class),
            $container->get(UserAccountService::class),
            $container->get(UserRoleService::class),
            $reviewService,
            $scoreService
        );
    }
}
