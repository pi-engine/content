<?php

namespace Content\Factory\Repository;

use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Content\Model\Item\Item;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Content\Repository\ItemRepository;


class ItemRepositoryFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @return ItemRepository
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ItemRepository
    {
        return new ItemRepository(
            $container->get(AdapterInterface::class),
            new ReflectionHydrator(),
            new Item(0, 0, 0, 0, 0,0, 0,0, 0),
        );
    }
}
