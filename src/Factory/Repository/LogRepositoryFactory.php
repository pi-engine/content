<?php

namespace Content\Factory\Repository;

use Content\Model\Log;
use Content\Model\Key;
use Content\Model\Meta;
use Content\Repository\LogRepository;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;


class LogRepositoryFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @return LogRepository
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LogRepository
    {
        return new LogRepository(
            $container->get(AdapterInterface::class),
            new ReflectionHydrator(),
            new Log(0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        );
    }
}
