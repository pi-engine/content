<?php

namespace Content;

use Laminas\Mvc\Middleware\PipeSpec;
use Laminas\Router\Http\Literal;
use User\Middleware\AuthenticationMiddleware;
use User\Middleware\AuthorizationMiddleware;
use User\Middleware\SecurityMiddleware;

return [
    'service_manager' => [
        'aliases' => [ 
            Repository\ItemRepositoryInterface::class => Repository\ItemRepository::class,
        ],
        'factories' => [

            Repository\ItemRepository::class => Factory\Repository\ItemRepositoryFactory::class,
            Service\ItemService::class => Factory\Service\ItemServiceFactory::class,
            Handler\Api\Item\ItemHandler::class => Factory\Handler\Api\Item\ItemHandlerFactory::class,
            Handler\Api\Item\ItemStoreHandler::class => Factory\Handler\Api\Item\ItemStoreHandlerFactory::class,
            Handler\Api\Item\ItemDetailHandler::class => Factory\Handler\Api\Item\ItemDetailHandlerFactory::class,
            Handler\Api\Item\ItemUpdateHandler::class => Factory\Handler\Api\Item\ItemUpdateHandlerFactory::class,
            Handler\Api\Item\ItemDeleteHandler::class => Factory\Handler\Api\Item\ItemDeleteHandlerFactory::class,
            Middleware\ValidationMiddleware::class        => Factory\Middleware\ValidationMiddlewareFactory::class,

        ],
    ],
    'router' => [
        'routes' => [
            // Api section
            'api_content' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/content',
                    'defaults' => [],
                ],
                'child_routes' => [
                    'content-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/item/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'content',
                                'handler' => 'list',
                                'permissions' => 'content-dashboard',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    Handler\Api\Item\ItemHandler::class
                                ),
                            ],
                        ],
                    ],
                    'content-store' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/item/store',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'send',
                                'handler' => 'send',
                                'permissions' => 'content-dashboard',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    Middleware\ValidationMiddleware::class,
                                    Handler\Api\Item\ItemStoreHandler::class
                                ),
                            ],
                        ],
                    ],
                    'content-detail' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/item/detail',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'detail',
                                'handler' => 'detail',
                                'permissions' => 'content-dashboard',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    Handler\Api\Item\ItemDetailHandler::class
                                ),
                            ],
                        ],
                    ],
                    'content-update' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/item/update',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'update',
                                'handler' => 'update',
                                'permissions' => 'content-dashboard',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    Handler\Api\Item\ItemUpdateHandler::class
                                ),
                            ],
                        ],
                    ],
                    'content-delete' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/item/delete',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'delete',
                                'handler' => 'delete',
                                'permissions' => 'content-dashboard',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    Handler\Api\Item\ItemDeleteHandler::class
                                ),
                            ],
                        ],
                    ],


                ],
            ],
        ],
    ],

    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
