<?php

namespace Content\Handler\Public\Dashboard;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DashboardHandler implements RequestHandlerInterface
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
        // Get request body
        $requestBody = $request->getParsedBody();

        $result = [];

        switch ($requestBody['caller']) {
            case 'shokrin':
                $result = [
                    "sliders" => [
                        [
                            "image" => "https://api.shokrin.com/upload/ver-03/slide-01.png?" . time(),
                            "mobile" => "https://api.shokrin.com/upload/ver-03/slide-01.png?" . time(),
                            "has_link" => true,
                            "url" => "/",
                            "button_title" => "خرید",
                            "title" => "شکرین",
                            "subhead" => " درخشش خانه تو",
                            "subtitle" => "",
                        ],
                        [
                            "image" => "https://api.shokrin.com/upload/ver-03/slide-02.png?" . time(),
                            "mobile" => "https://api.shokrin.com/upload/ver-03/slide-02.png?" . time(),
                            "has_link" => true,
                            "url" => "/",
                            "button_title" => "خرید",
                            "title" => "شکرین",
                            "subhead" => " درخشش خانه تو",
                            "subtitle" => "",
                        ],
                    ],
                    "special_section" => [
                        "list" => $this->itemService->getItemList(['type' => 'product', 'limit' => 6, 'page' => 3])['data']['list'],
                        "type" => "product",
                        "title" => "فروش ویژه",
                        "more_link" => "/products/",
                        "more_title" => "مشاهده بیشتر",
                        "background" => "https://api.shokrin.com/upload/ver-03/right-side-main.png",
                        "abstract" => ""
                    ],
                    "middle_banner" => [
                        "image" => "https://api.shokrin.com/upload/ver-03/middle-slider.png",
                        "mobile" => "https://api.shokrin.com/upload/ver-03/middle-slider.png",
                        "has_link" => true,
                        "url" => "/",
                        "button_title" => "انتخاب کن",
                        "title" => "قصه شکرین",
                        "subhead" => "با عشق شروع می شود",
                        "subtitle" => "",
                    ],
                    "old_sliders" => [
                        [
                            "image" => "https://api.shokrin.com/upload/images/new-face/slider/slider-1.jpg",
                            "mobile" => "https://api.shokrin.com/upload/images/new-face/slider/mob-1_768x940.jpg",
                            "title" => "قصه شکرین با عشق شروع میشه",
                            "subhead" => "عشق به تولید عشف به خلق کردن عشق به سازندگی",
                            "subtitle" => "عشق به کارآفرینی و اراﺋه محصولات با کیفیت ایرانی به سرزمینمون",
                        ],
                        [
                            "image" => "https://api.shokrin.com/upload/images/new-face/slider/slider-2.jpg",
                            "mobile" => "https://api.shokrin.com/upload/images/new-face/slider/mob-2_768x940.jpg",

                            "title" => "قصه شکرین با عشق شروع میشه",
                            "subhead" => "عشق به تولید عشف به خلق کردن عشق به سازندگی",
                            "subtitle" => "عشق به کارآفرینی و اراﺋه محصولات با کیفیت ایرانی به سرزمینمون",
                        ],
                        [
                            "image" =>
                                "https://api.shokrin.com/upload/images/new-face/slider/slider-3.jpg",
                            "mobile" =>
                                "https://api.shokrin.com/upload/images/new-face/slider/mob-3_768x940.jpg",

                            "title" => "قصه شکرین با عشق شروع میشه",
                            "subhead" => "عشق به تولید عشف به خلق کردن عشق به سازندگی",
                            "subtitle" => "عشق به کارآفرینی و اراﺋه محصولات با کیفیت ایرانی به سرزمینمون",
                        ],
                    ],
                    "banners" => [
                        [
                            "id" => 1,
                            "title" => "مجموعه جدیدترین ها",
                            "image" =>
                                "https://api.shokrin.com/upload/images/new-face/banner/banner-1.jpg",
                            "button" => "مشاهده محصولات",
                            "uri" => "shop/",
                            "url" => "",
                            "is_local" => true,
                        ],
                        [
                            "id" => 2,
                            "title" => "مجموعه مدروز",
                            "image" =>
                                "https://api.shokrin.com/upload/images/new-face/banner/banner-2.jpg",
                            "button" => "مشاهده محصولات",
                            "uri" => "shop/",
                            "url" => "",
                            "is_local" => true,
                        ],
                        [
                            "id" => 3,
                            "title" => "مجموعه پرفروش ترین ها",
                            "image" =>
                                "https://api.shokrin.com/upload/images/new-face/banner/banner-3.jpg",
                            "button" => "مشاهده محصولات",
                            "uri" => "shop/",
                            "url" => "",
                            "is_local" => true,
                        ],
                    ],
                    "products" => [
                        "id" => 1,
                        "category" => "",
                        "title" => "محصولات پرطرفدار",
                        "list" => $this->itemService->getItemList(['type' => 'product', 'limit' => 12, 'page' => 1])['data']['list'],

                    ],
                    "top_section" => [
                        "title" => "جدید ترین محصولات",
                        "more_link" => "/products/",
                        "more_title" => "مشاهده بیشتر",
                        "list" => $this->itemService->getItemList(['type' => 'product', 'limit' => 4, 'page' => 1])['data']['list'],
                    ],
                    "bottom_section" => [
                        "title" => "محصولات پر طرفدار",
                        "more_link" => "/products/",
                        "more_title" => "مشاهده بیشتر",
                        "list" => $this->itemService->getItemList(['type' => 'product', 'limit' => 6, 'page' => 2])['data']['list'],
                        "banner" => [
                            "image" => "https://api.shokrin.com/upload/ver-03/category-banner.png",
                            "title" => "شمعدان‌های کلاسیک",
                            "subtitle" => "محصولات جدید",
                            "button_link" => "/products/",
                            "button_title" => "خرید",
                        ]
                    ],
                    "freq_questions" => [
                        "banner" => "https://yadapi.kerloper.com/upload/faq.jpg",
                        "title" => "مراحل تولید محصوالت شکرین",
                        "sub_title" => "مراحــل تولیــد محصــوالت در مجموعــه مــا بــه ایــن شــکل هســت
کـه بعـد از خریـد آهـن بـرای تولیـد یـک محصـول درجـه یـک بـه
ترتیــب مراحــل زیــر اجــرا میشــه :",
                        "questions" => [
                            [
                                "question" => "خمکاری",
                                "answer" => "تـو ایـن مرحلـه بـه ورق هـا فـرم مـورد نظرمـون رو میدیـم یـا بـه
اصطــالح اســپینینگ می کنیــم",
                            ],
                            [
                                "question" => "جوشکاری و شاخه کشی",
                                "answer" => "بـا اسـتفاده از جـوش برنـج ، قطعـات رو بـه هـم وصـل می کنیـم تـا
شـکل مـورد نظرمـون تکمیـل بشـه
بـا اسـتفاده از جـوش برنـج ، قطعـات رو بـه هـم وصـل می کنیـم تـا
شـکل مـورد نظرمـون تکمیـل بشـه",
                            ],
                            [
                                "question" => "پرداخت کاری",
                                "answer" => "در ایــن مرحلــه ســطح کار رو صیقلــی ، صاف ، پولیــش و جــلا میدیم
تابرای مرحلــه بعــد کــه آبکاری هست کاملا آمــاده بشــه ، در واقــع
پرداخــت کاری زیرســازی محصوله کــه از چندین مرحله تشکیل میشه و
مــوارد اســتفاده متعدد داره .
مثــلا بــرای شاخه های لوســتر ، ابتــدا اسیدشویی و بعــد پسیلــه گیری
می کینــم تــا جــای جوش از بیــن بــره.
برای کارهای پرداخت کاری هم ابتدا نفت شویی انجام میشه.",
                            ],
                            [
                                "question" => "آبکاری",
                                "answer" => "آبـکاری پوشـش دهی سـطح فلـزی هسـت کـه پرداخـت شـده تـا فلـز حالـت
تزئینـی بگیـره
ـد ســاله داره کــه آبــکاری شــکرین بیــش از نیــم قــرن
ُ
آبــکاری پیشــینه صـ
ســابقه درخشــان در ایــن زمینــه داره
از مزایـای آبـکاری می تونیـم بـه مهارخوردگی ، پوشـش دادن بـرای تزئینات
اشــیا ، ســفت و ســخت شــدن اشــیا ، محافظــت در برابــر تابــش ، کاهــش
اصطـکاک و ... اشـاره کـرد
آبکاری به چند روش انجام میشه :
در یــک روش ، ســطح جامــد بــا ورق فلــزی پوشــیده میشــه و بــرای اتصــال
اون از فشــار و گرمــا اســتفاده میشــه
در روش دیگــه ، تکنیــک پوشــش رســوب بخــار تحــت خــال و الیــه نشــانی
پاششــی هســت",
                            ],
                            [
                                "question" => "مونتاژ قطعات",
                                "answer" => "در ایـن مرحلـه ، قطعـات رو روی هـم سـوار و اسـمبل می کنیـم و بعـد کارهـا
میـرن بـرای تسـت و کنتـرل کیفـی و بعـد هـم بسـته بندی میشـن و بـرای
فـروش گذاشـته میشـن",
                            ],
                        ],
                    ],
                    "parallex" => [
                        "right" => [
                            "image" =>
                                "https://api.shokrin.com/upload/images/new-face/parallex/banner-6-1.jpg",
                            "title" => "شکرین ، درخشش خانه تو",
                            "text" => "تولید کننده ایرانی آباژور، لوستر، شمعدان و ساعت",
                            "uri" => "shop/",
                            "url" => "",
                            "button" => "مشاهده فروشگاه",
                            "is_local" => true,
                        ],
                        "left" => [
                            [
                                "image" =>
                                    "https://api.shokrin.com/upload/images/new-face/parallex/banner-6-4-2.jpg",
                                "title" => "تخفیف استثنایی",
                                "text" => "موجودی محدود",
                                "uri" => "shop/",
                                "url" => "",
                                "button" => "مشاهده فروشگاه",
                                "is_local" => true,
                            ],
                            [
                                "image" =>
                                    "https://api.shokrin.com/upload/images/new-face/parallex/banner-6-4-1.jpg",
                                "title" => "",
                                "text" => "",
                                "uri" => "",
                                "url" => "",
                                "button" => "",
                                "is_local" => true,
                            ],
                            [
                                "image" =>
                                    "https://api.shokrin.com/upload/images/new-face/parallex/banner-6-4-4.jpg",
                                "title" => "",
                                "text" => "",
                                "uri" => "",
                                "url" => "",
                                "button" => "",
                                "is_local" => true,
                            ],
                            [
                                "image" =>
                                    "https://api.shokrin.com/upload/images/new-face/parallex/banner-6-4-3.jpg",
                                "title" => "پیشنهاد شگفت انگیز",
                                "text" => "تخفیف عالی",
                                "uri" => "shop/",
                                "url" => "",
                                "button" => "مشاهده فروشگاه",
                                "is_local" => true,
                            ],
                        ],
                    ],
                    "amazing_offer" => [
                        "id" => 1,
                        "slug" => "slug1",
                        "thumbnail" => "https://api.shokrin.com/upload/images/493x484.png",
                        "badge" => null,
                        "rate" => 3.5,
                        "title" => "آباژور کنارسالنی کد ۲۶۱",
                        "price" => "۱,۵۳۰,۰۰۰ تومان",
                        "price_del" => "۱,۵۵۰,۰۰۰ تومان",
                        "description" =>
                            " توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول توضیحات برای تست نمایش محصول",
                    ],
                    "blog_list" => [

                        "title" => "وبلاگ شکرین",
                        "more_link" => "/pages/",
                        "more_title" => "مشاهده بیشتر",
                        "list" => [

                            [
                                "id" => 1,
                                "slug" => "our_story",
                                "thumbnail" =>
                                    "https://api.shokrin.com/upload/images/800x562-1.png",
                                "author" => "ادمین",
                                "tag" => "طراحی داخلی",
                                "title" => "داستان ما",
                                "description" =>
                                    " توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ",
                                "date" => "17 آبان 1401",
                            ],
                            [
                                "id" => 2,
                                "slug" => "dehydration_method",
                                "thumbnail" =>
                                    "https://api.shokrin.com/upload/images/800x562-2.png",
                                "author" => "ادمین",
                                "tag" => "طراحی داخلی",
                                "title" => "روش اب کاری محصولات شکرین",
                                "description" =>
                                    " توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ",
                                "date" => "10 آذر 1401",
                            ],
                            [
                                "id" => 3,
                                "slug" => "light_importance",
                                "thumbnail" =>
                                    "https://api.shokrin.com/upload/images/800x562-3.png",
                                "author" => "ادمین",
                                "tag" => "طراحی داخلی",
                                "title" => " دلیل اهمیت نور در دکوراسیون فضای داخلی",
                                "description" =>
                                    " توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ",
                                "date" => "23 آذر 1401",
                            ],
                        ],
                    ],
                    "old_blog_list" => [
                        [
                            "id" => 1,
                            "slug" => "our_story",
                            "thumbnail" =>
                                "https://api.shokrin.com/upload/images/800x562-1.png",
                            "author" => "ادمین",
                            "tag" => "طراحی داخلی",
                            "title" => "داستان ما",
                            "description" =>
                                " توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ",
                            "date" => "17 آبان 1401",
                        ],
                        [
                            "id" => 2,
                            "slug" => "dehydration_method",
                            "thumbnail" =>
                                "https://api.shokrin.com/upload/images/800x562-2.png",
                            "author" => "ادمین",
                            "tag" => "طراحی داخلی",
                            "title" => "روش اب کاری محصولات شکرین",
                            "description" =>
                                " توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ",
                            "date" => "10 آذر 1401",
                        ],
                        [
                            "id" => 3,
                            "slug" => "light_importance",
                            "thumbnail" =>
                                "https://api.shokrin.com/upload/images/800x562-3.png",
                            "author" => "ادمین",
                            "tag" => "طراحی داخلی",
                            "title" => " دلیل اهمیت نور در دکوراسیون فضای داخلی",
                            "description" =>
                                " توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ توضیحات برای تست نمایش بلاگ",
                            "date" => "23 آذر 1401",
                        ],
                    ],
                    "testimonial" => [
                        [
                            "id" => 1,
                            "slug" => "slug1",
                            "title" => "تیتر1",
                            "thumbnail" => "https://api.shokrin.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست1",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                        [
                            "id" => 2,
                            "slug" => "slug2",
                            "title" => "تیتر2",
                            "thumbnail" => "https://api.shokrin.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست2",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                        [
                            "id" => 3,
                            "slug" => "slug3",
                            "title" => "تیتر3",
                            "thumbnail" => "https://api.shokrin.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست3",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                        [
                            "id" => 4,
                            "slug" => "slug4",
                            "title" => "تیتر4",
                            "thumbnail" => "https://api.shokrin.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست4",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                        [
                            "id" => 5,
                            "slug" => "slug5",
                            "title" => "تیتر5",
                            "thumbnail" => "https://api.shokrin.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست5",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                    ],
                    "instagram_section" => [
                        "list" => $this->itemService->getItemList(['type' => 'product', 'limit' => 4, 'page' => 6])['data']['list'],
                        "type" => "product",
                        "title" => "فروش ویژه",
                        "more_link" => "/products/",
                        "more_title" => "مشاهده بیشتر",
                        "background" => "https://api.shokrin.com/upload/ver-03/right-side-main.png",
                        "abstract" => ""
                    ],
                ];
        }

        // Set result
        $result = [
            'result' => true,
            'data' => $result,
            'error' => [],
        ];

        return new JsonResponse($result);
    }
}
