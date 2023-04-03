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
            Repository\LogRepositoryInterface::class => Repository\LogRepository::class,
        ],
        'factories' => [
            Repository\ItemRepository::class => Factory\Repository\ItemRepositoryFactory::class,
            Service\ItemService::class => Factory\Service\ItemServiceFactory::class,
            Repository\LogRepository::class => Factory\Repository\LogRepositoryFactory::class,
            Service\LogService::class => Factory\Service\LogServiceFactory::class,
            Service\MetaService::class => Factory\Service\MetaServiceFactory::class,
            Middleware\ValidationMiddleware::class => Factory\Middleware\ValidationMiddlewareFactory::class,
            Validator\SlugValidator::class => Factory\Validator\SlugValidatorFactory::class,
            Validator\TypeValidator::class => Factory\Validator\TypeValidatorFactory::class,
            Handler\Api\MainHandler::class => Factory\Handler\Api\MainHandlerFactory::class,
            Handler\Api\ItemListHandler::class => Factory\Handler\Api\ItemListHandlerFactory::class,
            Handler\Api\ItemDetailHandler::class => Factory\Handler\Api\ItemDetailHandlerFactory::class,
            Handler\Admin\ItemAddHandler::class => Factory\Handler\Admin\ItemAddHandlerFactory::class,
            Handler\Admin\ItemListHandler::class => Factory\Handler\Admin\ItemListHandlerFactory::class,
            Handler\Admin\ItemDetailHandler::class => Factory\Handler\Admin\ItemDetailHandlerFactory::class,
            Handler\Admin\ItemEditHandler::class => Factory\Handler\Admin\ItemEditHandlerFactory::class,
            Handler\Admin\ItemDeleteHandler::class => Factory\Handler\Admin\ItemDeleteHandlerFactory::class,
            Handler\InstallerHandler::class => Factory\Handler\InstallerHandlerFactory::class,

            // Cart services factory
            Handler\Api\Cart\AddHandler::class => Factory\Handler\Api\Cart\AddHandlerFactory::class,
            Handler\Api\Cart\ListHandler::class => Factory\Handler\Api\Cart\ListHandlerFactory::class,
            Handler\Api\Cart\UpdateHandler::class => Factory\Handler\Api\Cart\UpdateHandlerFactory::class,

            // Order services factory
            Handler\Api\Order\AddHandler::class => Factory\Handler\Api\Order\AddHandlerFactory::class,
            Handler\Api\Order\ListHandler::class => Factory\Handler\Api\Order\ListHandlerFactory::class,

            // Question services factory
            Handler\Api\Question\AddHandler::class => Factory\Handler\Api\Question\AddHandlerFactory::class,
            Handler\Api\Question\ListHandler::class => Factory\Handler\Api\Question\ListHandlerFactory::class,
            Handler\Api\Question\ReplyHandler::class => Factory\Handler\Api\Question\ReplyHandlerFactory::class,
            Handler\Api\Question\GetHandler::class => Factory\Handler\Api\Question\GetHandlerFactory::class,

            // Support services factory
            Handler\Api\Support\AddHandler::class => Factory\Handler\Api\Support\AddHandlerFactory::class,
            Handler\Api\Support\ListHandler::class => Factory\Handler\Api\Support\ListHandlerFactory::class,
            Handler\Api\Support\ReplyHandler::class => Factory\Handler\Api\Support\ReplyHandlerFactory::class,
            Handler\Api\Support\GetHandler::class => Factory\Handler\Api\Support\GetHandlerFactory::class,

            // Location services factory
            Handler\Api\Location\MarkListHandler::class => Factory\Handler\Api\Location\MarkListHandlerFactory::class,
            Handler\Api\Location\MarkDetailHandler::class => Factory\Handler\Api\Location\MarkDetailHandlerFactory::class,

            // Category services factory
            Handler\Api\Category\CategoryListHandler::class => Factory\Handler\Api\Category\CategoryListHandlerFactory::class,

            // Setting services factory
            Handler\Api\Setting\VersionHandler::class => Factory\Handler\Api\Setting\VersionHandlerFactory::class,

            // Reservation services factory
            Handler\Api\Reservation\ReserveHandler::class => Factory\Handler\Api\Reservation\ReserveHandlerFactory::class,
            Handler\Api\Reservation\ReservationRemoveHandler::class => Factory\Handler\Api\Reservation\ReservationRemoveHandlerFactory::class,
            Handler\Api\Reservation\ReservationListHandler::class => Factory\Handler\Api\Reservation\ReservationListHandlerFactory::class,

            // Opinion services factory
            Handler\Api\Opinion\LikeHandler::class => Factory\Handler\Api\Opinion\LikeHandlerFactory::class,
            Handler\Api\Opinion\DislikeHandler::class => Factory\Handler\Api\Opinion\DislikeHandlerFactory::class,


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
                    'main' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/main',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'main',
                                'validator' => 'main',
                                'handler' => 'main',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    Handler\Api\MainHandler::class
                                ),
                            ],
                        ],
                    ],
                    'list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    Middleware\ValidationMiddleware::class,
                                    Handler\Api\ItemListHandler::class
                                ),
                            ],
                        ],
                    ],
                    'detail' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/detail',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'detail',
                                'handler' => 'detail',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    Middleware\ValidationMiddleware::class,
                                    Handler\Api\ItemDetailHandler::class
                                ),
                            ],
                        ],
                    ],


                    /// Cart services
                    ///
                    'add-cart' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/cart/add',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Cart\AddHandler::class
                                ),
                            ],
                        ],
                    ],
                    'update-cart' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/cart/update',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Cart\UpdateHandler::class
                                ),
                            ],
                        ],
                    ],
                    'delete-cart' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/cart/delete',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'delete',
                                'permissions' => 'item-delete',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Cart\DeleteHandler::class
                                ),
                            ],
                        ],
                    ],
                    'cart-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/cart/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Cart\ListHandler::class
                                ),
                            ],
                        ],
                    ],

                    /// Order services
                    ///
                    'add-order' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/order/add',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Order\AddHandler::class
                                ),
                            ],
                        ],
                    ],
                    'order-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/order/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Order\ListHandler::class
                                ),
                            ],
                        ],
                    ],

                    /// Address services
                    ///
                    'add-address' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/address/add',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Address\AddHandler::class
                                ),
                            ],
                        ],
                    ],
                    'address-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/address/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Address\ListHandler::class
                                ),
                            ],
                        ],
                    ],

                    /// Question services
                    ///
                    'add-question' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/question/add',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Question\AddHandler::class
                                ),
                            ],
                        ],
                    ],
                    'reply-question' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/question/reply',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Question\ReplyHandler::class
                                ),
                            ],
                        ],
                    ],
                    'question-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/question/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Question\ListHandler::class
                                ),
                            ],
                        ],
                    ],
                    'get-question' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/question/get',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Question\GetHandler::class
                                ),
                            ],
                        ],
                    ],

                    /// Support services
                    ///
                    'add-support' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/support/add',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Support\AddHandler::class
                                ),
                            ],
                        ],
                    ],
                    'reply-support' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/support/reply',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Support\ReplyHandler::class
                                ),
                            ],
                        ],
                    ],
                    'support-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/support/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
//                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Support\ListHandler::class
                                ),
                            ],
                        ],
                    ],
                    'get-support' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/support/get',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Support\GetHandler::class
                                ),
                            ],
                        ],
                    ],

                    /// Location services
                    ///
                    'location-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/location/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Location\MarkListHandler::class
                                ),
                            ],
                        ],
                    ],
                    'get-location' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/location/get',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Location\MarkDetailHandler::class
                                ),
                            ],
                        ],
                    ],

                    /// Category services
                    ///
                    'category-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/category/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Category\CategoryListHandler::class
                                ),
                            ],
                        ],
                    ],

                    /// Setting services
                    ///
                    'setting' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/setting/version',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Setting\VersionHandler::class
                                ),
                            ],
                        ],
                    ],


                    /// Reservation service
                    ///
                    'reserve' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/reserve',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Reservation\ReserveHandler::class
                                ),
                            ],
                        ],
                    ],
                    'reserve-remove' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/reserve/remove',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Reservation\ReservationRemoveHandler::class
                                ),
                            ],
                        ],
                    ],
                    'reserve-list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/reserve/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Reservation\ReservationListHandler::class
                                ),
                            ],
                        ],
                    ],

                    /// Opinion service
                    ///
                    'opinion-like' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/opinion/like',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Opinion\LikeHandler::class
                                ),
                            ],
                        ],
                    ],
                    'opinion-dislike' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/opinion/dislike',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'validator' => 'list',
                                'handler' => 'list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                    Handler\Api\Opinion\DislikeHandler::class
                                ),
                            ],
                        ],
                    ],


                ],
            ],
            // Admin section
            'admin_content' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/admin/content',
                    'defaults' => [],
                ],
                'child_routes' => [
                    'list' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/list',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'admin',
                                'package' => 'item',
                                'handler' => 'list',
                                'permissions' => 'item-list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemListHandler::class
                                ),
                            ],
                        ],
                    ],
                    'detail' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/detail',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'item',
                                'handler' => 'detail',
                                'permissions' => 'item-detail',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemDetailHandler::class
                                ),
                            ],
                        ],
                    ],
                    'add' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/add',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'admin',
                                'package' => 'item',
                                'handler' => 'add',
                                'permissions' => 'item-add',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemAddHandler::class
                                ),
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/edit',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'admin',
                                'package' => 'item',
                                'handler' => 'edit',
                                'permissions' => 'item-edit',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    Handler\Admin\ItemEditHandler::class
                                ),
                            ],
                        ],
                    ],
                    'delete' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/delete',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'admin',
                                'package' => 'item',
                                'handler' => 'delete',
                                'permissions' => 'item-delete',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
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
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/installer',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'admin',
                                'package' => 'installer',
                                'handler' => 'installer',
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