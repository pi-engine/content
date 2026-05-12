<?php

namespace Content;

use Content\Middleware\ValidationMiddleware;
use Laminas\Mvc\Middleware\PipeSpec;
use Laminas\Router\Http\Literal;
use User\Middleware\AuthenticationMiddleware;
use User\Middleware\AuthorizationMiddleware;
use User\Middleware\RequestPreparationMiddleware;
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
            Handler\Public\Tourism\DashboardHandler::class => Factory\Handler\Public\Tourism\DashboardHandlerFactory::class,
            Handler\Public\Tourism\Tour\GetHandler::class => Factory\Handler\Public\Tourism\Tour\GetHandlerFactory::class,
            Handler\Public\Tourism\Tour\ListHandler::class => Factory\Handler\Public\Tourism\Tour\ListHandlerFactory::class,
            Handler\Public\Tourism\Blog\BlogGetHandler::class => Factory\Handler\Public\Tourism\Blog\BlogGetHandlerFactory::class,
            Handler\Public\Tourism\Blog\BlogListHandler::class => Factory\Handler\Public\Tourism\Blog\BlogListHandlerFactory::class,
            Handler\Public\Tourism\Travelogue\TravelogueGetHandler::class => Factory\Handler\Public\Tourism\Travelogue\TravelogueGetHandlerFactory::class,
            Handler\Public\Tourism\Travelogue\TravelogueListHandler::class => Factory\Handler\Public\Tourism\Travelogue\TravelogueListHandlerFactory::class,
            Handler\Public\Tourism\Destination\GetHandler::class => Factory\Handler\Public\Tourism\Destination\GetHandlerFactory::class,
            Handler\Public\Tourism\Destination\ListHandler::class => Factory\Handler\Public\Tourism\Destination\ListHandlerFactory::class,
            Handler\Public\Tourism\Main\MainHandler::class => Factory\Handler\Public\Tourism\Main\MainHandlerFactory::class,


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
            Handler\Admin\Entity\EntityRemoveHandler::class => Factory\Handler\Admin\Entity\EntityRemoveHandlerFactory::class,
            Handler\Admin\Entity\EntityUpdateHandler::class => Factory\Handler\Admin\Entity\EntityUpdateHandlerFactory::class,
            Handler\Admin\Entity\EntityReplaceHandler::class => Factory\Handler\Admin\Entity\EntityReplaceHandlerFactory::class,
            Handler\Admin\Entity\EntityListHandler::class => Factory\Handler\Admin\Entity\EntityListHandlerFactory::class,
            Handler\Admin\Entity\EntityGetHandler::class => Factory\Handler\Admin\Entity\EntityGetHandlerFactory::class,

            // Item
            Handler\Admin\Item\ItemListHandler::class => Factory\Handler\Admin\Item\ItemListHandlerFactory::class,
            Handler\Admin\Item\ItemDetailHandler::class => Factory\Handler\Admin\Item\ItemDetailHandlerFactory::class,

            // Meta
            Handler\Admin\Meta\Key\MetaKeyListHandler::class => Factory\Handler\Admin\Meta\Key\MetaKeyListHandlerFactory::class,
            Handler\Admin\Meta\Key\MetaKeyAddHandler::class => Factory\Handler\Admin\Meta\Key\MetaKeyAddHandlerFactory::class,
            Handler\Admin\Meta\Key\MetaKeyUpdateHandler::class => Factory\Handler\Admin\Meta\Key\MetaKeyUpdateHandlerFactory::class,
            Handler\Admin\Meta\Key\MetaKeyGetHandler::class => Factory\Handler\Admin\Meta\Key\MetaKeyGetHandlerFactory::class,
            Handler\Admin\Meta\Value\MetaValueAddHandler::class => Factory\Handler\Admin\Meta\Value\MetaValueAddHandlerFactory::class,
            Handler\Admin\Meta\Value\MetaValueListHandler::class => Factory\Handler\Admin\Meta\Value\MetaValueListHandlerFactory::class,

            // Supplier (admin)
            Content\Service\SupplierService::class => Content\Factory\Service\SupplierServiceFactory::class,
            Handler\Admin\Supplier\AddHandler::class => Factory\Handler\Admin\Supplier\AddHandlerFactory::class,
            Handler\Admin\Supplier\ListHandler::class => Factory\Handler\Admin\Supplier\ListHandlerFactory::class,
            Handler\User\Supplier\ListHandler::class => Factory\Handler\User\Supplier\ListHandlerFactory::class,
            Handler\User\Supplier\GetHandler::class => Factory\Handler\User\Supplier\GetHandlerFactory::class,
            Handler\Admin\Supplier\GetHandler::class => Factory\Handler\Admin\Supplier\GetHandlerFactory::class,
            Handler\Admin\Supplier\EditHandler::class => Factory\Handler\Admin\Supplier\EditHandlerFactory::class,
            Handler\Admin\Supplier\DeleteHandler::class => Factory\Handler\Admin\Supplier\DeleteHandlerFactory::class,
            // Material (admin)
            Content\Service\MaterialService::class => Content\Factory\Service\MaterialServiceFactory::class,
            Handler\Admin\Material\AddHandler::class => Factory\Handler\Admin\Material\AddHandlerFactory::class,
            Handler\Admin\Material\ListHandler::class => Factory\Handler\Admin\Material\ListHandlerFactory::class,
            Handler\Admin\Material\GetHandler::class => Factory\Handler\Admin\Material\GetHandlerFactory::class,
            Handler\Admin\Material\EditHandler::class => Factory\Handler\Admin\Material\EditHandlerFactory::class,
            // Material offer (supplier: رکورد میزان ساخت و مبلغ واحد برای هر ماده)
            Service\MaterialOfferService::class => Factory\Service\MaterialOfferServiceFactory::class,
            Handler\Admin\MaterialOffer\AddHandler::class => Factory\Handler\Admin\MaterialOffer\AddHandlerFactory::class,
            Handler\Admin\MaterialOffer\ListHandler::class => Factory\Handler\Admin\MaterialOffer\ListHandlerFactory::class,
            Handler\Admin\MaterialOffer\EditHandler::class => Factory\Handler\Admin\MaterialOffer\EditHandlerFactory::class,
            Handler\Admin\MaterialOffer\DeleteHandler::class => Factory\Handler\Admin\MaterialOffer\DeleteHandlerFactory::class,
            Handler\Admin\MaterialOffer\PricingContextHandler::class => Factory\Handler\Admin\MaterialOffer\PricingContextHandlerFactory::class,
            Handler\User\MaterialOffer\ListHandler::class => Factory\Handler\User\MaterialOffer\ListHandlerFactory::class,
            // Supplier review (user + admin)
            Repository\SupplierReviewRepository::class => Factory\Repository\SupplierReviewRepositoryFactory::class,
            Service\SupplierReviewService::class => Factory\Service\SupplierReviewServiceFactory::class,
            Handler\User\SupplierReview\AddHandler::class => Factory\Handler\User\SupplierReview\AddHandlerFactory::class,
            Handler\User\SupplierReview\GetMyRatingHandler::class => Factory\Handler\User\SupplierReview\GetMyRatingHandlerFactory::class,
            Handler\User\SupplierReview\AddRatingHandler::class => Factory\Handler\User\SupplierReview\AddRatingHandlerFactory::class,
            Handler\User\SupplierReview\AddCommentHandler::class => Factory\Handler\User\SupplierReview\AddCommentHandlerFactory::class,
            Handler\User\SupplierReview\ListHandler::class => Factory\Handler\User\SupplierReview\ListHandlerFactory::class,
            Handler\Admin\SupplierReview\ListHandler::class => Factory\Handler\Admin\SupplierReview\ListHandlerFactory::class,
            Handler\Admin\SupplierReview\UpdateStatusHandler::class => Factory\Handler\Admin\SupplierReview\UpdateStatusHandlerFactory::class,
            // Supplier multi-type scores
            Repository\SupplierScoreTypeRepository::class => Factory\Repository\SupplierScoreTypeRepositoryFactory::class,
            Repository\SupplierScoreRepository::class => Factory\Repository\SupplierScoreRepositoryFactory::class,
            Service\SupplierScoreService::class => Factory\Service\SupplierScoreServiceFactory::class,
            Handler\User\SupplierReview\GetScoreTypesHandler::class => Factory\Handler\User\SupplierReview\GetScoreTypesHandlerFactory::class,
            Handler\User\SupplierReview\AddScoresHandler::class => Factory\Handler\User\SupplierReview\AddScoresHandlerFactory::class,
            Handler\User\SupplierReview\GetMyScoresHandler::class => Factory\Handler\User\SupplierReview\GetMyScoresHandlerFactory::class,
            Handler\User\SupplierReview\GetSupplierScoreAveragesHandler::class => Factory\Handler\User\SupplierReview\GetSupplierScoreAveragesHandlerFactory::class,
            // Industry (admin)
            Content\Service\IndustryService::class => Content\Factory\Service\IndustryServiceFactory::class,
            Handler\Admin\Industry\ListHandler::class => Factory\Handler\Admin\Industry\ListHandlerFactory::class,
            Handler\Admin\Industry\GetHandler::class => Factory\Handler\Admin\Industry\GetHandlerFactory::class,
            Handler\Admin\Industry\AddHandler::class => Factory\Handler\Admin\Industry\AddHandlerFactory::class,
            Handler\Admin\Industry\EditHandler::class => Factory\Handler\Admin\Industry\EditHandlerFactory::class,
            Handler\Admin\Industry\DeleteHandler::class => Factory\Handler\Admin\Industry\DeleteHandlerFactory::class,

            ///Public Section
            // Item
            Handler\Public\Item\ItemListHandler::class => Factory\Handler\Public\Item\ItemListHandlerFactory::class,
            Handler\Public\Item\ItemDetailHandler::class => Factory\Handler\Public\Item\ItemDetailHandlerFactory::class,

            // Dashboard
            Handler\Public\Dashboard\DashboardHandler::class => Factory\Handler\Public\Dashboard\DashboardHandlerFactory::class,

            // Dashboard
            Handler\Public\Information\InformationAddressHandler::class => Factory\Handler\Public\Information\InformationAddressHandlerFactory::class,

            // Meta
            Handler\Public\Meta\Key\MetaKeyListHandler::class => Factory\Handler\Public\Meta\Key\MetaKeyListHandlerFactory::class,
            Handler\Public\Meta\Value\MetaValueListHandler::class => Factory\Handler\Public\Meta\Value\MetaValueListHandlerFactory::class,
            Handler\Public\Meta\Value\IndustrySubIndustryListHandler::class => Factory\Handler\Public\Meta\Value\IndustrySubIndustryListHandlerFactory::class,

            Handler\Public\Supplier\RegisterHandler::class => Factory\Handler\Public\Supplier\RegisterHandlerFactory::class,
            Handler\Public\Workflow\IndustriesMaterialsHandler::class => Factory\Handler\Public\Workflow\IndustriesMaterialsHandlerFactory::class,
            Handler\Public\Workflow\VendorsHandler::class => Factory\Handler\Public\Workflow\VendorsHandlerFactory::class,
            Handler\Public\Workflow\VendorsAndMaterialsHandler::class => Factory\Handler\Public\Workflow\VendorsAndMaterialsHandlerFactory::class,
            Service\WorkflowDataService::class => Factory\Service\WorkflowDataServiceFactory::class,

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

                    'dashboard' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/dashboard',
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
                                            RequestPreparationMiddleware::class,
                                            Handler\Public\Item\ItemDetailHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'public',
                                        'package' => 'item',
                                        'handler' => 'get',
                                        'permission' => 'public-content-dashboard-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
//                                            SecurityMiddleware::class,
                                            RequestPreparationMiddleware::class,
                                            Handler\Public\Dashboard\DashboardHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
                    ],
                    'information' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/information',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/address',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'public',
                                        'package' => 'item',
                                        'handler' => 'get',
                                        'permission' => 'public-content-information-address',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            Handler\Public\Information\InformationAddressHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ]
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
                                            RequestPreparationMiddleware::class,
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
                                                    RequestPreparationMiddleware::class,
                                                    SecurityMiddleware::class,
                                                    Handler\Public\Meta\Value\MetaValueListHandler::class
                                                ),
                                            ],
                                        ],
                                    ],
                                    'industry-list' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/industry-list',
                                            'defaults' => [
                                                'module' => 'content',
                                                'section' => 'public',
                                                'package' => 'item',
                                                'handler' => 'industry-list',
                                                'permission' => 'public-content-item-list',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    RequestPreparationMiddleware::class,
                                                    SecurityMiddleware::class,
                                                    Handler\Public\Meta\Value\IndustrySubIndustryListHandler::class
                                                ),
                                            ],
                                        ],
                                    ],

                                ]
                            ],


                        ]
                    ],

                    'supplier' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/supplier',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'register' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/register',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'public',
                                        'package' => 'item',
                                        'handler' => 'register',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            Handler\Public\Supplier\RegisterHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],

                    'workflow' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/workflow',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'industries-materials' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/industries-materials',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'public',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            Handler\Public\Workflow\IndustriesMaterialsHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'vendors' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/vendors',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'public',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            Handler\Public\Workflow\VendorsHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'vendors-and-materials' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/vendors-and-materials',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'public',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            Handler\Public\Workflow\VendorsAndMaterialsHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
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
                                            Handler\Public\Tourism\DashboardHandler::class
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
                                                    Handler\Public\Tourism\Tour\GetHandler::class
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
                                                    Handler\Public\Tourism\Tour\ListHandler::class
                                                ),
                                            ],
                                        ],

                                    ],
                                ],
                            ],
                            'blog' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/blog',
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
                                                    Handler\Public\Tourism\Blog\BlogGetHandler::class
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
                                                    Handler\Public\Tourism\Blog\BlogListHandler::class
                                                ),
                                            ],
                                        ],

                                    ],
                                ],
                            ],
                            'travelogue' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/travelogue',
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
                                                    Handler\Public\Tourism\Travelogue\TravelogueGetHandler::class
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
                                                    Handler\Public\Tourism\Travelogue\TravelogueListHandler::class
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
                                                    Handler\Public\Tourism\Destination\GetHandler::class
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
                                                    Handler\Public\Tourism\Destination\ListHandler::class
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
                                                    Handler\Public\Tourism\Main\MainHandler::class
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
                                                    Handler\Public\Tourism\Destination\ListHandler::class
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
                                    AuthenticationMiddleware::class,
                                    AuthorizationMiddleware::class,
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
                                            \Order\Handler\Api\Address\AddressAddHandler::class
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
                                            \Order\Handler\Api\Address\AddressListHandler::class
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
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
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
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
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
                                            RequestPreparationMiddleware::class,
//                                            SecurityMiddleware::class,
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
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
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
                                    RequestPreparationMiddleware::class,
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
                    'supplier' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/supplier',
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
                                        'package' => 'item',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-item-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Supplier\AddHandler::class
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
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Supplier\ListHandler::class
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
                                        'package' => 'item',
                                        'handler' => 'get',
                                        'permission' => 'admin-content-item-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Supplier\GetHandler::class
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
                                        'permission' => 'admin-content-item-edit',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Supplier\EditHandler::class
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
                                        'permission' => 'admin-content-item-delete',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Supplier\DeleteHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'supplier-review' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/supplier-review',
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
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\SupplierReview\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'update-status' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/update-status',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'item',
                                        'handler' => 'edit',
                                        'permission' => 'admin-content-item-edit',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\SupplierReview\UpdateStatusHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'material' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/material',
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
                                        'package' => 'item',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-item-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Material\AddHandler::class
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
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Material\ListHandler::class
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
                                        'package' => 'item',
                                        'handler' => 'get',
                                        'permission' => 'admin-content-item-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Material\GetHandler::class
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
                                        'permission' => 'admin-content-item-edit',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Material\EditHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'material-offer' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/material-offer',
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
                                        'package' => 'item',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-item-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\MaterialOffer\AddHandler::class
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
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\MaterialOffer\ListHandler::class
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
                                        'permission' => 'admin-content-item-edit',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\MaterialOffer\EditHandler::class
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
                                        'permission' => 'admin-content-item-delete',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\MaterialOffer\DeleteHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'pricing-context' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/pricing-context',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'item',
                                        'handler' => 'pricing-context',
                                        'permission' => 'admin-content-item-list',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
//                                            AuthorizationMiddleware::class,
                                            Handler\Admin\MaterialOffer\PricingContextHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'industry' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/industry',
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
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Industry\ListHandler::class
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
                                        'package' => 'item',
                                        'handler' => 'get',
                                        'permission' => 'admin-content-item-get',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Industry\GetHandler::class
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
                                        'permission' => 'admin-content-item-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Industry\AddHandler::class
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
                                        'permission' => 'admin-content-item-edit',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Industry\EditHandler::class
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
                                        'permission' => 'admin-content-item-edit',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Industry\DeleteHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'meta' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/meta',
                            'defaults' => [],
                        ],
                        'child_routes' => [

                            'key/add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/key/add',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'item',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-item-add',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            RequestPreparationMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Meta\Key\MetaKeyAddHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'key/update' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/key/update',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'item',
                                        'handler' => 'add',
                                        'permission' => 'admin-content-item-update',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            RequestPreparationMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Meta\Key\MetaKeyUpdateHandler::class
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
                                    'get' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/get',
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
                                                    Handler\Admin\Meta\Key\MetaKeyGetHandler::class
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
                                                    RequestPreparationMiddleware::class,
                                                    AuthenticationMiddleware::class,
                                                    AuthorizationMiddleware::class,
                                                    Handler\Admin\Meta\Value\MetaValueListHandler::class
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
                                                'handler' => 'list',
                                                'permission' => 'admin-content-item-list',
                                                'controller' => PipeSpec::class,
                                                'middleware' => new PipeSpec(
                                                    SecurityMiddleware::class,
                                                    RequestPreparationMiddleware::class,
                                                    AuthenticationMiddleware::class,
                                                    AuthorizationMiddleware::class,
                                                    Handler\Admin\Meta\Value\MetaValueAddHandler::class
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
                            'remove' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/remove',
                                    'defaults' => [
                                        'module' => 'content',
                                        'section' => 'admin',
                                        'package' => 'entity',
                                        'handler' => 'remove',
                                        'permission' => 'admin-content-entity-remove',
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            AuthorizationMiddleware::class,
                                            Handler\Admin\Entity\EntityRemoveHandler::class
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
            // User section (read-only: suppliers + pricing for logged-in users)
            'user_content' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/user/content',
                    'defaults' => [],
                ],
                'child_routes' => [
                    'supplier' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/supplier',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/list',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\Supplier\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'get' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\Supplier\GetHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'material-offer' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/material-offer',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/list',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\MaterialOffer\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'supplier-review' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/supplier-review',
                            'defaults' => [],
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\AddHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'get-my-rating' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get-my-rating',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\GetMyRatingHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'add-rating' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add-rating',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\AddRatingHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'add-comment' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add-comment',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\AddCommentHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/list',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\ListHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'score-types' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/score-types',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\GetScoreTypesHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'add-scores' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add-scores',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\AddScoresHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'get-my-scores' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get-my-scores',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\GetMyScoresHandler::class
                                        ),
                                    ],
                                ],
                            ],
                            'get-supplier-score-averages' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/get-supplier-score-averages',
                                    'defaults' => [
                                        'controller' => PipeSpec::class,
                                        'middleware' => new PipeSpec(
                                            RequestPreparationMiddleware::class,
                                            SecurityMiddleware::class,
                                            AuthenticationMiddleware::class,
                                            Handler\User\SupplierReview\GetSupplierScoreAveragesHandler::class
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

    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];