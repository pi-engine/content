<?php

namespace Content\Handler\Public\Information;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InformationAddressHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    protected ResponseFactoryInterface $responseFactory;

    /** @var StreamFactoryInterface */
    protected StreamFactoryInterface $streamFactory;

    /** @var ItemService */
    protected ItemService $itemService;


    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface   $streamFactory,
        ItemService              $itemService
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->itemService = $itemService;
    }

    /// TODO: check slug and check public access
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = [
            [
                "name" => "تهران",
                "slug" => "tehran",
                "level" => "state",
                "level_name" => "استان",
                "children" => [
                    [
                        "name" => "تهران",
                        "slug" => "tehran-city",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                            [
                                "name" => "پاسداران",
                                "slug" => "pasdaran",
                                "level" => "district",
                                "level_name" => "محله"
                            ],
                            [
                                "name" => "شهرک غرب",
                                "slug" => "shahrak-gharb",
                                "level" => "district",
                                "level_name" => "محله"
                            ]
                        ]
                    ],
                    [
                        "name" => "ری",
                        "slug" => "rey",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                            [
                                "name" => "عبدالعظیم",
                                "slug" => "abdolazim",
                                "level" => "district",
                                "level_name" => "محله"
                            ]
                        ]
                    ]
                ]
            ],
            [
                "name" => "اصفهان",
                "slug" => "isfahan",
                "level" => "state",
                "level_name" => "استان",
                "children" => [
                    [
                        "name" => "اصفهان",
                        "slug" => "isfahan-city",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                            [
                                "name" => "احمدآباد",
                                "slug" => "ahmadabad",
                                "level" => "district",
                                "level_name" => "محله"
                            ],
                            [
                                "name" => "چهارباغ",
                                "slug" => "chaharbagh",
                                "level" => "district",
                                "level_name" => "محله"
                            ]
                        ]
                    ],
                    [
                        "name" => "کاشان",
                        "slug" => "kashan",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                            [
                                "name" => "فین",
                                "slug" => "fin",
                                "level" => "district",
                                "level_name" => "محله"
                            ]
                        ]
                    ]
                ]
            ],
            [
                "name" => "خراسان رضوی",
                "slug" => "khorasan-razavi",
                "level" => "state",
                "level_name" => "استان",
                "children" => [
                    [
                        "name" => "مشهد",
                        "slug" => "mashhad",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                            [
                                "name" => "طرقبه",
                                "slug" => "torghabe",
                                "level" => "district",
                                "level_name" => "محله"
                            ],
                            [
                                "name" => "شاندیز",
                                "slug" => "shandiz",
                                "level" => "district",
                                "level_name" => "محله"
                            ]
                        ]
                    ],
                    [
                        "name" => "نیشابور",
                        "slug" => "neyshabur",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                        ]
                    ]
                ]
            ],
            [
                "name" => "فارس",
                "slug" => "fars",
                "level" => "state",
                "level_name" => "استان",
                "children" => [
                    [
                        "name" => "شیراز",
                        "slug" => "shiraz",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                            [
                                "name" => "قصرالدشت",
                                "slug" => "ghasr-dasht",
                                "level" => "district",
                                "level_name" => "محله"
                            ],
                            [
                                "name" => "معالی آباد",
                                "slug" => "maali-abad",
                                "level" => "district",
                                "level_name" => "محله"
                            ]
                        ]
                    ],
                    [
                        "name" => "مرودشت",
                        "slug" => "marvdasht",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                        ]
                    ]
                ]
            ],
            [
                "name" => "خوزستان",
                "slug" => "khuzestan",
                "level" => "state",
                "level_name" => "استان",
                "children" => [
                    [
                        "name" => "اهواز",
                        "slug" => "ahvaz",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                            [
                                "name" => "کوی ملت",
                                "slug" => "kuy-mellat",
                                "level" => "district",
                                "level_name" => "محله"
                            ],
                            [
                                "name" => "کوی گلستان",
                                "slug" => "kuy-golestan",
                                "level" => "district",
                                "level_name" => "محله"
                            ]
                        ]
                    ],
                    [
                        "name" => "آبادان",
                        "slug" => "abadan",
                        "level" => "city",
                        "level_name" => "شهر",
                        "children" => [
                        ]
                    ]
                ]
            ]
        ];



        // Set result
        $result = [
            'result' => true,
            'data' => $result,
            'error' => [],
        ];

        return new JsonResponse($result);
    }
}
