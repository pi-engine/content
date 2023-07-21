<?php

namespace Content;

use Content\Middleware\ValidationMiddleware;
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


            // Report services factory
            Handler\Api\Report\Club\Score\ListHandler::class => Factory\Handler\Api\Report\Club\Score\ListHandlerFactory::class,

            // Tourism services factory
            Handler\Api\Tourism\DashboardHandler::class => Factory\Handler\Api\Tourism\DashboardHandlerFactory::class,
            Handler\Api\Tourism\Tour\GetHandler::class => Factory\Handler\Api\Tourism\Tour\GetHandlerFactory::class,
            Handler\Api\Tourism\Tour\ListHandler::class => Factory\Handler\Api\Tourism\Tour\ListHandlerFactory::class,
            Handler\Api\Tourism\Destination\GetHandler::class => Factory\Handler\Api\Tourism\Destination\GetHandlerFactory::class,
            Handler\Api\Tourism\Destination\ListHandler::class => Factory\Handler\Api\Tourism\Destination\ListHandlerFactory::class,
            Handler\Api\Tourism\Main\MainHandler::class => Factory\Handler\Api\Tourism\Main\MainHandlerFactory::class,


            ///Admin Section
            // Support services factory
            Handler\Admin\Support\AddHandler::class => Factory\Handler\Admin\Support\AddHandlerFactory::class,
            Handler\Admin\Support\ListHandler::class => Factory\Handler\Admin\Support\ListHandlerFactory::class,
            Handler\Admin\Support\ReplyHandler::class => Factory\Handler\Admin\Support\ReplyHandlerFactory::class,
            Handler\Admin\Support\GetHandler::class => Factory\Handler\Admin\Support\GetHandlerFactory::class,

            // Order services factory
            Handler\Admin\Order\ListHandler::class => Factory\Handler\Admin\Order\ListHandlerFactory::class,

            // Entity service factory
            Handler\Admin\Entity\EntityAddHandler::class => Factory\Handler\Admin\Entity\EntityAddHandlerFactory::class,
            Handler\Admin\Entity\EntityUpdateHandler::class => Factory\Handler\Admin\Entity\EntityUpdateHandlerFactory::class,
            Handler\Admin\Entity\EntityReplaceHandler::class => Factory\Handler\Admin\Entity\EntityReplaceHandlerFactory::class,
            Handler\Admin\Entity\EntityListHandler::class => Factory\Handler\Admin\Entity\EntityListHandlerFactory::class,
            Handler\Admin\Entity\EntityGetHandler::class => Factory\Handler\Admin\Entity\EntityGetHandlerFactory::class,

            // Item
            Handler\Admin\Item\ItemListHandler::class => Factory\Handler\Admin\Item\ItemListHandlerFactory::class,
            Handler\Admin\Item\ItemDetailHandler::class => Factory\Handler\Admin\Item\ItemDetailHandlerFactory::class,

            // Meta
            Handler\Admin\Meta\Key\MetaKeyListHandler::class => Factory\Handler\Admin\Meta\Key\MetaKeyListHandlerFactory::class,
            Handler\Admin\Meta\Value\MetaValueListHandler::class => Factory\Handler\Admin\Meta\Value\MetaValueListHandlerFactory::class,




            ///Public Section
            // Item
            Handler\Public\Item\ItemListHandler::class => Factory\Handler\Public\Item\ItemListHandlerFactory::class,
            Handler\Public\Item\ItemDetailHandler::class => Factory\Handler\Public\Item\ItemDetailHandlerFactory::class,

            // Meta
            Handler\Public\Meta\Key\MetaKeyListHandler::class => Factory\Handler\Public\Meta\Key\MetaKeyListHandlerFactory::class,
            Handler\Public\Meta\Value\MetaValueListHandler::class => Factory\Handler\Public\Meta\Value\MetaValueListHandlerFactory::class,


        ],
    ],

    'router' => [
        'routes' => [
            // Public section
            'public_content' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/public/content',
                    'defaults' => [],
                ],
                'child_routes' => [

                    'item' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/item',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'public',
                                        'package' => 'item',
                                        'handler' => 'get',
                                        'permission' => 'public-content-item-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            Handler\Public\Item\ItemDetailHandler::class
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
                                        'section' => 'public',
                                        'package' => 'item',
                                        'handler' => 'list',
                                        'permission' => 'public-content-item-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            Handler\Public\Item\ItemListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],
                    'meta' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/meta',
                            'defaults' => [],
                        ],
                        'child_routes' => [

                            'list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/list',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'public',
                                        'package' => 'item',
                                        'handler' => 'list',
                                        'permission' => 'public-content-item-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            Handler\Public\Meta\Key\MetaKeyListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'key' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/key',
                                    'defaults' => [],
                                ],
                                'child_routes' => [

                                    'list' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/list',
                                            'defaults' => [
                                                'module' => 'content',
                                                'section' => 'public',
                                                'package' => 'item',
                                                'handler' => 'list',
                                                'permission' => 'public-content-item-list',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    Handler\Public\Meta\Key\MetaKeyListHandler::class
                                                ),
                                            ],
                                        ],
                                    ],

                                ]
                            ],
                            'value' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/value',
                                    'defaults' => [],
                                ],
                                'child_routes' => [

                                    'list' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/list',
                                            'defaults' => [
                                                'module' => 'content',
                                                'section' => 'public',
                                                'package' => 'item',
                                                'handler' => 'list',
                                                'permission' => 'public-content-item-list',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    Handler\Public\Meta\Value\MetaValueListHandler::class
                                                ),
                                            ],
                                        ],
                                    ],

                                ]
                            ],


                        ]
                    ],

                    'tourism' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/tourism',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'dashboard' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/dashboard',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'tourism',
                                        'handler' => 'dashboard',
                                        'permission' => 'api-content-tourism-dashboard',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            Handler\Api\Tourism\DashboardHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'tour' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/tour',
                                    'defaults' => [],
                                ],
                                'child_routes' => [
                                    'get' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/get',
                                            'defaults' => [
                                                'module' => 'content',
                                                'section' => 'api',
                                                'package' => 'tourism',
                                                'handler' => 'tour',
                                                'permission' => 'api-content-tourism-tour',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    Handler\Api\Tourism\Tour\GetHandler::class
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
                                                'package' => 'tourism',
                                                'handler' => 'tour',
                                                'permission' => 'api-content-tourism-tour',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    Handler\Api\Tourism\Tour\ListHandler::class
                                                ),
                                            ],
                                        ],

                                    ],
                                ],
                            ],
                            'destination' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/destination',
                                    'defaults' => [],
                                ],
                                'child_routes' => [
                                    'get' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/get',
                                            'defaults' => [
                                                'module' => 'content',
                                                'section' => 'api',
                                                'package' => 'tourism',
                                                'handler' => 'tour',
                                                'permission' => 'api-content-tourism-destination',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    Handler\Api\Tourism\Destination\GetHandler::class
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
                                                'package' => 'tourism',
                                                'handler' => 'tour',
                                                'permission' => 'api-content-tourism-destination',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    Handler\Api\Tourism\Destination\ListHandler::class
                                                ),
                                            ],
                                        ],

                                    ],
                                ],
                            ],
                            'main' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/main',
                                    'defaults' => [],
                                ],
                                'child_routes' => [
                                    'get' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/data',
                                            'defaults' => [
                                                'module' => 'content',
                                                'section' => 'api',
                                                'package' => 'tourism',
                                                'handler' => 'tour',
                                                'permission' => 'api-content-tourism-destination',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    Handler\Api\Tourism\Main\MainHandler::class
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
                                                'package' => 'tourism',
                                                'handler' => 'tour',
                                                'permission' => 'api-content-tourism-destination',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    Handler\Api\Tourism\Destination\ListHandler::class
                                                ),
                                            ],
                                        ],

                                    ],
                                ],
                            ],
                        ],
                    ],

                ],
            ],
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
                                'permission' => 'api-content-main',
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
                                'permissions' => 'api-item-list',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    Middleware\ValidationMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
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
                                'permission' => 'api-content-detail',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    Middleware\ValidationMiddleware::class,
                                    Handler\Api\ItemDetailHandler::class
                                ),
                            ],
                        ],
                    ],

                    'cart' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/cart',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'cart',
                                        'handler' => 'add',
                                        'permission' => 'api-content-cart-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                           ///TODO: resolve and uncomment this
                                           // AuthorizationMiddleware::class,
                                            Handler\Api\Cart\AddHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'update' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/update',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'cart',
                                        'handler' => 'update',
                                        'permission' => 'api-content-cart-update',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Cart\UpdateHandler::class
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
                                        'section' => 'api',
                                        'package' => 'cart',
                                        'handler' => 'delete',
                                        'permission' => 'api-content-cart-delete',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Cart\DeleteHandler::class
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
                                        'package' => 'cart',
                                        'handler' => 'list',
                                        'permission' => 'api-content-cart-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            ///TODO: resolve and uncomment this
                                           // AuthorizationMiddleware::class,
                                            Handler\Api\Cart\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],

                    'order' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/order',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'order',
                                        'handler' => 'add',
                                        'permission' => 'api-content-order-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Order\AddHandler::class
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
                                        'package' => 'order',
                                        'handler' => 'list',
                                        'permission' => 'api-content-order-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Order\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],

                    'address' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/address',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'address',
                                        'handler' => 'add',
                                        'permission' => 'api-content-address-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Address\AddHandler::class
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
                                        'package' => 'address',
                                        'handler' => 'list',
                                        'permission' => 'api-content-address-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Address\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],

                    'question' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/question',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'question',
                                        'handler' => 'add',
                                        'permission' => 'api-content-question-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Question\AddHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'reply' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/reply',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'question',
                                        'handler' => 'reply',
                                        'permission' => 'api-content-question-reply',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Question\ReplyHandler::class
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
                                        'package' => 'question',
                                        'handler' => 'list',
                                        'permission' => 'api-content-question-list',
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
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'question',
                                        'handler' => 'get',
                                        'permission' => 'api-content-question-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Question\GetHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],
                    'support' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/support',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'support',
                                        'handler' => 'add',
                                        'permission' => 'api-content-support-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Support\AddHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'reply' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/reply',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'support',
                                        'handler' => 'reply',
                                        'permission' => 'api-content-support-reply',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Support\ReplyHandler::class
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
                                        'package' => 'support',
                                        'handler' => 'list',
                                        'permission' => 'api-content-support-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Support\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'support',
                                        'handler' => 'get',
                                        'permission' => 'api-content-support-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Support\GetHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],

                    'location' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/location',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/list',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'location',
                                        'handler' => 'list',
                                        'permission' => 'api-content-location-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Location\MarkListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'location',
                                        'handler' => 'get',
                                        'permission' => 'api-content-location-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Location\MarkDetailHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],

                    'category' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/category',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'category-list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/list',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'category',
                                        'handler' => 'list',
                                        'permission' => 'api-content-category-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
//                                    AuthenticationMiddleware::class,
//                                    AuthorizationMiddleware::class,
                                            Handler\Api\Category\CategoryListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],


                    'setting' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/setting',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'setting' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/version',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'setting',
                                        'handler' => 'version',
                                        'permission' => 'api-content-setting-version',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            Handler\Api\Setting\VersionHandler::class
                                        ),
                                    ],
                                ],
                            ],

                        ]
                    ],

                    'reserve-add' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/reserve',
                            'defaults' => [
                                'module' => 'content',
                                'section' => 'api',
                                'package' => 'reserve',
                                'handler' => 'add',
                                'permission' => 'api-content-reserve-add',
                                'validator' => 'reserve',
                                'controller' => PipeSpec::class,
                                'middleware' => new PipeSpec(
                                    SecurityMiddleware::class,
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
                                    ValidationMiddleware::class,
                                    Handler\Api\Reservation\ReserveHandler::class
                                ),
                            ],
                        ],
                    ],
                    'reserve' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/reserve',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'remove' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/remove',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'reserve',
                                        'handler' => 'remove',
                                        'permission' => 'api-content-reserve-remove',
                                        'validator' => 'reserve',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            ValidationMiddleware::class,
                                            Handler\Api\Reservation\ReservationRemoveHandler::class
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
                                        'package' => 'reserve',
                                        'handler' => 'list',
                                        'permission' => 'api-content-reserve-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Reservation\ReservationListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],

                    'opinion' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/opinion',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'like' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/like',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'opinion',
                                        'handler' => 'like',
                                        'permission' => 'api-content-opinion-like',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Opinion\LikeHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'dislike' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/dislike',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'api',
                                        'package' => 'opinion',
                                        'handler' => 'dislike',
                                        'permission' => 'api-content-opinion-dislike',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Api\Opinion\DislikeHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],

                    'report' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/report',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'club' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/club',
                                    'defaults' => [],
                                ],
                                'child_routes' => [
                                    'score' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/score',
                                            'defaults' => [],
                                        ],
                                        'child_routes' => [
                                            'list' => [
                                                'type' => Literal::class,
                                                'options' => [
                                                    'route' => '/list',
                                                    'defaults' => [
                                                        'module' => 'content',
                                                        'section' => 'api',
                                                        'package' => 'report',
                                                        'handler' => 'club',
                                                        'permission' => 'api-content-report-club',
                                                        'controller' => PipeSpec::class,
                                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                                            AuthenticationMiddleware::class,
                                                            AuthorizationMiddleware::class,
                                                            Handler\Api\Report\Club\Score\ListHandler::class
                                                        ),
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
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

                    'item' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/item',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'item',
                                        'handler' => 'get',
                                        'permission' => 'admin-content-item-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Item\ItemDetailHandler::class
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
                                        'section' => 'admin',
                                        'package' => 'item',
                                        'handler' => 'list',
                                        'permission' => 'admin-content-item-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Item\ItemListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],
                    'meta' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/meta',
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
                                        'permission' => 'admin-content-item-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Meta\Key\MetaKeyListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'key' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/key',
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
                                                'permission' => 'admin-content-item-list',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    AuthenticationMiddleware::class,
                                                    AuthorizationMiddleware::class,
                                                    Handler\Admin\Meta\Key\MetaKeyListHandler::class
                                                ),
                                            ],
                                        ],
                                    ],

                                ]
                            ],
                            'value' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/value',
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
                                                'permission' => 'admin-content-item-list',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    AuthenticationMiddleware::class,
                                                    AuthorizationMiddleware::class,
                                                    Handler\Admin\Meta\Value\MetaValueListHandler::class
                                                ),
                                            ],
                                        ],
                                    ],

                                ]
                            ],


                        ]
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

                    'support' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/support',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'support',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-support-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Support\AddHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'reply' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/reply',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'support',
                                        'handler' => 'reply',
                                        'permission' => 'admin-content-support-reply',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Support\ReplyHandler::class
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
                                        'section' => 'admin',
                                        'package' => 'support',
                                        'handler' => 'list',
                                        'permission' => 'admin-content-support-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Support\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'support',
                                        'handler' => 'get',
                                        'permission' => 'admin-content-support-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Support\GetHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],

                    'order' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/order',
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
                                        'package' => 'order',
                                        'handler' => 'list',
                                        'permission' => 'admin-content-order-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Order\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],
                    'entity' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/entity',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'entity',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-entity-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Entity\EntityAddHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'update' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/update',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'entity',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-entity-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Entity\EntityUpdateHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'replace' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/replace',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'entity',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-entity-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Entity\EntityReplaceHandler::class
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
                                        'section' => 'admin',
                                        'package' => 'entity',
                                        'handler' => 'list',
                                        'permission' => 'admin-content-entity-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Entity\EntityListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'entity',
                                        'handler' => 'list',
                                        'permission' => 'admin-content-entity-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Entity\EntityGetHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
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