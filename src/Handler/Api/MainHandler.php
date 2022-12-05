<?php

namespace Content\Handler\Api;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MainHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    protected ResponseFactoryInterface $responseFactory;

    /** @var StreamFactoryInterface */
    protected StreamFactoryInterface $streamFactory;

    /** @var ItemService */
    protected ItemService $itemService;


    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        ItemService $itemService
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory   = $streamFactory;
        $this->itemService     = $itemService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get request body
        $requestBody = $request->getParsedBody();

        // Get list of notifications


        $result = [
            "sliders" => [
                "slide01" => [
                    "image" => "https://shokrin.veriainfotech.com/upload/images/922x467.png",
                    "title" => "شکرین",
                    "subhead" => "درخشش خانه تو",
                    "subtitle" => " تولید کننده ایرانی آباژور، لوستر، شمعدان و ساعت"
                ],
                "slide02" => [
                    "image" => "https://shokrin.veriainfotech.com/upload/images/1100x801.png",
                    "title" => "شکرین",
                    "subhead" => "درخشش خانه تو",
                    "subtitle" => " تولید کننده ایرانی آباژور، لوستر، شمعدان و ساعت"
                ]
            ],
            "banners" => [
                "banner01" => [
                    "image" => "https://shokrin.veriainfotech.com/upload/images/565x550.png"
                ],
                "banner02" => [
                    "image" => "https://shokrin.veriainfotech.com/upload/images/650x295-1.png"
                ],
                "banner03" => [
                    "image" => "https://shokrin.veriainfotech.com/upload/images/650x295-2.png"
                ]
            ],
            "product_category" => [
                [
                    "id" => 1,
                    "title" => "آباژو",
                    "products" => [
                        [
                            "id" => 1,
                            "slug" => "slug1",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-1.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 2,
                            "slug" => "slug2",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-2.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 3,
                            "slug" => "slug3",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-3.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 4,
                            "slug" => "slug4",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-4.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 5,
                            "slug" => "slug5",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-5.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ]
                    ]
                ],
                [
                    "id" => 2,
                    "title" => "ساعت",
                    "products" => [
                        [
                            "id" => 8,
                            "slug" => "slug8",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-8.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 9,
                            "slug" => "slug9",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-9.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 10,
                            "slug" => "slug10",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-10.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 11,
                            "slug" => "slug11",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-11.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 12,
                            "slug" => "slug12",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-12.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ]
                    ]
                ],
                [
                    "id" => 3,
                    "title" => "اکسسوری",
                    "products" => [
                        [
                            "id" => 13,
                            "slug" => "slug13",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-13.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 14,
                            "slug" => "slug14",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-14.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 15,
                            "slug" => "slug15",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-15.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 16,
                            "slug" => "slug16",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-16.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 17,
                            "slug" => "slug17",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-17.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ]
                    ]
                ],
                [
                    "id" => 4,
                    "title" => "شمعدان",
                    "products" => [
                        [
                            "id" => 18,
                            "slug" => "slug18",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-18.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 19,
                            "slug" => "slug19",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-19.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 20,
                            "slug" => "slug20",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-20.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 21,
                            "slug" => "slug21",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-21.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 22,
                            "slug" => "slug22",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-22.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ]
                    ]
                ],
                [
                    "id" => 5,
                    "title" => "لوستر و آویز",
                    "products" => [
                        [
                            "id" => 23,
                            "slug" => "slug23",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-23.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 24,
                            "slug" => "slug24",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-24.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 25,
                            "slug" => "slug25",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-25.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 26,
                            "slug" => "slug26",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-26.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ],
                        [
                            "id" => 27,
                            "slug" => "slug27",
                            "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/600x600-27.png",
                            "badge" => null,
                            "categories" => "آباژو",
                            "rate" => 3.5,
                            "title" => "محصول کد ۲۶۱",
                            "price" => "۱,۵۳۰,۰۰۰ تومان",
                            "price_del" => null,
                            "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
                        ]
                    ]
                ]
            ],
            "amazing_offer" => [
                "id" => 1,
                "slug" => "slug1",
                "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/493x484.png",
                "badge" => null,
                "rate" => 3.5,
                "title" => "آباژور کنارسالنی کد ۲۶۱",
                "price" => "۱,۵۳۰,۰۰۰ تومان",
                "price_del" => "۱,۵۵۰,۰۰۰ تومان",
                "description" => " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول"
            ],
            "blog_list" => [
                [
                    "id" => 1,
                    "slug" => "slug1",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/800x562-1.png",
                    "author" => "ادمین",
                    "tag" => "طراحی داخلی",
                    "title" => " دلیل اهمیت نور در دکوراسیون فضای داخلی",
                    "date" => "آبان ۱۷, ۱۴۰۰"
                ],
                [
                    "id" => 2,
                    "slug" => "slug2",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/800x562-2.png",
                    "author" => "ادمین",
                    "tag" => "طراحی داخلی",
                    "title" => " دلیل اهمیت نور در دکوراسیون فضای داخلی",
                    "date" => "آبان ۱۷, ۱۴۰۰"
                ],
                [
                    "id" => 3,
                    "slug" => "slug3",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/800x562-3.png",
                    "author" => "ادمین",
                    "tag" => "طراحی داخلی",
                    "title" => " دلیل اهمیت نور در دکوراسیون فضای داخلی",
                    "date" => "آبان ۱۷, ۱۴۰۰"
                ],
                [
                    "id" => 4,
                    "slug" => "slug4",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/800x562-4.png",
                    "author" => "ادمین",
                    "tag" => "طراحی داخلی",
                    "title" => " دلیل اهمیت نور در دکوراسیون فضای داخلی",
                    "date" => "آبان ۱۷, ۱۴۰۰"
                ],
                [
                    "id" => 5,
                    "slug" => "slug5",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/800x562-5.png",
                    "author" => "ادمین",
                    "tag" => "طراحی داخلی",
                    "title" => " دلیل اهمیت نور در دکوراسیون فضای داخلی",
                    "date" => "آبان ۱۷, ۱۴۰۰"
                ]
            ],
            "testimonial" => [
                [
                    "id" => 1,
                    "slug" => "slug1",
                    "title" => "تیتر1",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/400x400.jpg",
                    "caption" => " کپشن پست1",
                    "date" => "آبان ۱۷, ۱۴۰۱"
                ],
                [
                    "id" => 2,
                    "slug" => "slug2",
                    "title" => "تیتر2",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/400x400.jpg",
                    "caption" => " کپشن پست2",
                    "date" => "آبان ۱۷, ۱۴۰۱"
                ],
                [
                    "id" => 3,
                    "slug" => "slug3",
                    "title" => "تیتر3",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/400x400.jpg",
                    "caption" => " کپشن پست3",
                    "date" => "آبان ۱۷, ۱۴۰۱"
                ],
                [
                    "id" => 4,
                    "slug" => "slug4",
                    "title" => "تیتر4",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/400x400.jpg",
                    "caption" => " کپشن پست4",
                    "date" => "آبان ۱۷, ۱۴۰۱"
                ],
                [
                    "id" => 5,
                    "slug" => "slug5",
                    "title" => "تیتر5",
                    "thumbnail" => "https://shokrin.veriainfotech.com/upload/images/400x400.jpg",
                    "caption" => " کپشن پست5",
                    "date" => "آبان ۱۷, ۱۴۰۱"
                ]
            ]
        ];



        // Set result
        $result = [
            'result' => true,
            'data'   => $result,
            'error'  => [],
        ];

        return new JsonResponse($result);
    }
}
