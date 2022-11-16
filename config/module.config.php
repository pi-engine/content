<?php

namespace Content;

use Laminas\Mvc\Middleware\PipeSpec;
use Laminas\Router\Http\Literal;
use User\Middleware\AuthenticationMiddleware;
use User\Middleware\AuthorizationMiddleware;
use User\Middleware\SecurityMiddleware;

return [
    'service_manager' => [
        'aliases'   => [
            Repository\ItemRepositoryInterface::class => Repository\ItemRepository::class,
        ],
        'factories' => [
            Repository\ItemRepository::class       => Factory\Repository\ItemRepositoryFactory::class,
            Service\ItemService::class             => Factory\Service\ItemServiceFactory::class,
            Handler\Api\ItemListHandler::class     => Factory\Handler\Api\ItemListHandlerFactory::class,
            Handler\Api\ItemDetailHandler::class   => Factory\Handler\Api\ItemDetailHandlerFactory::class,
            Handler\Admin\ItemAddHandler::class    => Factory\Handler\Admin\ItemAddHandlerFactory::class,
            Handler\Admin\ItemListHandler::class   => Factory\Handler\Admin\ItemListHandlerFactory::class,
            Handler\Admin\ItemDetailHandler::class => Factory\Handler\Admin\ItemDetailHandlerFactory::class,
            Handler\Admin\ItemEditHandler::class   => Factory\Handler\Admin\ItemEditHandlerFactory::class,
            Handler\Admin\ItemDeleteHandler::class => Factory\Handler\Admin\ItemDeleteHandlerFactory::class,
            Handler\InstallerHandler::class        => Factory\Handler\InstallerHandlerFactory::class,
        ],
    ],

    'router' => [
        'routes' => [
            // Api section
            'api_content'   => [
                'type'         => Literal::class,
                'options'      => [
                    'route'    => '/content',
                    'defaults' => [],
                ],
                'child_routes' => [
                    'list'   => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/list',
                            'defaults' => [
                                'module'     => 'content',
                                'section'    => 'api',
                                'package'    => 'item',
                                'handler'    => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    Handler\Api\ItemListHandler::class
                                ),
                            ],
                        ],
                    ],
                    'detail' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/detail',
                            'defaults' => [
                                'module'     => 'content',
                                'section'    => 'api',
                                'package'    => 'item',
                                'handler'    => 'detail',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    Handler\Api\ItemDetailHandler::class
                                ),
                            ],
                        ],
                    ],
                ],
            ],
            // Admin section
            'admin_content' => [
                'type'         => Literal::class,
                'options'      => [
                    'route'    => '/admin/content',
                    'defaults' => [],
                ],
                'child_routes' => [
                    'list'      => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/list',
                            'defaults' => [
                                'module'      => 'content',
                                'section'     => 'admin',
                                'package'     => 'item',
                                'handler'     => 'list',
                                'permissions' => 'item-list',
                                'controller'  => PipeSpec::class,
                                'middleware'  => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemListHandler::class
                                ),
                            ],
                        ],
                    ],
                    'detail'    => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/detail',
                            'defaults' => [
                                'module'      => 'content',
                                'section'     => 'api',
                                'package'     => 'item',
                                'handler'     => 'detail',
                                'permissions' => 'item-detail',
                                'controller'  => PipeSpec::class,
                                'middleware'  => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemDetailHandler::class
                                ),
                            ],
                        ],
                    ],
                    'add'       => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'module'      => 'content',
                                'section'     => 'admin',
                                'package'     => 'item',
                                'handler'     => 'add',
                                'permissions' => 'item-add',
                                'controller'  => PipeSpec::class,
                                'middleware'  => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemAddHandler::class
                                ),
                            ],
                        ],
                    ],
                    'edit'      => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/edit',
                            'defaults' => [
                                'module'      => 'content',
                                'section'     => 'admin',
                                'package'     => 'item',
                                'handler'     => 'edit',
                                'permissions' => 'item-edit',
                                'controller'  => PipeSpec::class,
                                'middleware'  => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemEditHandler::class
                                ),
                            ],
                        ],
                    ],
                    'delete'    => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/delete',
                            'defaults' => [
                                'module'      => 'content',
                                'section'     => 'admin',
                                'package'     => 'item',
                                'handler'     => 'delete',
                                'permissions' => 'item-delete',
                                'controller'  => PipeSpec::class,
                                'middleware'  => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemDeleteHandler::class
                                ),
                            ],
                        ],
                    ],
                    // Admin installer
                    'installer' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/installer',
                            'defaults' => [
                                'module'     => 'content',
                                'section'    => 'admin',
                                'package'    => 'installer',
                                'handler'    => 'installer',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    Handler\InstallerHandler::class
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