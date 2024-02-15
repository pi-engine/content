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
                $sliders = $this->itemService->getItem('shokrin-slider-2023', 'slug');
                $result = [

                    "sliders" => isset($sliders['banner_list']) ? $sliders['banner_list'] : [],
                    "special_section" => [
                        "list" => $this->itemService->getItemList([
                            'type' => 'product',
                            'special_suggest' => 1,
                            'limit' => 6,
                            'page' => 1
                        ])['data']['list'],
                        "type" => "product",
                        "title" => "فروش ویژه",
                        "button_link" => "/products/?specialProducts=true",
                        "more_title" => "مشاهده بیشتر",
                        "background" => "https://api.topinbiz.com/upload/ver-03/right-side-main.png",
                        "abstract" => ""
                    ],
                    "middle_banner" => [
                        "image" => "https://api.topinbiz.com/upload/ver-03/middle-slider.png",
                        "mobile" => "https://api.topinbiz.com/upload/ver-03/middle-slider.png",
                        "has_link" => true,
                        "url" => "/products/chandelier-code-401/",
                        "button_title" => "انتخاب کن",
                        "title" => "شکرین",
                        "subhead" => " درخشش خانه تو",
                        "subtitle" => "",
                    ],
                    "old_sliders" => [
                        [
                            "image" => "https://api.topinbiz.com/upload/images/new-face/slider/slider-1.jpg",
                            "mobile" => "https://api.topinbiz.com/upload/images/new-face/slider/mob-1_768x940.jpg",
                            "title" => "قصه شکرین با عشق شروع میشه",
                            "subhead" => "عشق به تولید عشف به خلق کردن عشق به سازندگی",
                            "subtitle" => "عشق به کارآفرینی و اراﺋه محصولات با کیفیت ایرانی به سرزمینمون",
                        ],
                        [
                            "image" => "https://api.topinbiz.com/upload/images/new-face/slider/slider-2.jpg",
                            "mobile" => "https://api.topinbiz.com/upload/images/new-face/slider/mob-2_768x940.jpg",

                            "title" => "قصه شکرین با عشق شروع میشه",
                            "subhead" => "عشق به تولید عشف به خلق کردن عشق به سازندگی",
                            "subtitle" => "عشق به کارآفرینی و اراﺋه محصولات با کیفیت ایرانی به سرزمینمون",
                        ],
                        [
                            "image" =>
                                "https://api.topinbiz.com/upload/images/new-face/slider/slider-3.jpg",
                            "mobile" =>
                                "https://api.topinbiz.com/upload/images/new-face/slider/mob-3_768x940.jpg",

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
                                "https://api.topinbiz.com/upload/images/new-face/banner/banner-1.jpg",
                            "button" => "مشاهده محصولات",
                            "uri" => "shop/",
                            "url" => "",
                            "is_local" => true,
                        ],
                        [
                            "id" => 2,
                            "title" => "مجموعه مدروز",
                            "image" =>
                                "https://api.topinbiz.com/upload/images/new-face/banner/banner-2.jpg",
                            "button" => "مشاهده محصولات",
                            "uri" => "shop/",
                            "url" => "",
                            "is_local" => true,
                        ],
                        [
                            "id" => 3,
                            "title" => "مجموعه پرفروش ترین ها",
                            "image" =>
                                "https://api.topinbiz.com/upload/images/new-face/banner/banner-3.jpg",
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
                        "list" => $this->itemService->getItemList(['type' => 'product', 'limit' => 8, 'page' => 2])['data']['list'],
                        "banner" => [
                            "image" => "https://api.topinbiz.com/upload/ver-03/category-banner.png",
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
                                "https://api.topinbiz.com/upload/images/new-face/parallex/banner-6-1.jpg",
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
                                    "https://api.topinbiz.com/upload/images/new-face/parallex/banner-6-4-2.jpg",
                                "title" => "تخفیف استثنایی",
                                "text" => "موجودی محدود",
                                "uri" => "shop/",
                                "url" => "",
                                "button" => "مشاهده فروشگاه",
                                "is_local" => true,
                            ],
                            [
                                "image" =>
                                    "https://api.topinbiz.com/upload/images/new-face/parallex/banner-6-4-1.jpg",
                                "title" => "",
                                "text" => "",
                                "uri" => "",
                                "url" => "",
                                "button" => "",
                                "is_local" => true,
                            ],
                            [
                                "image" =>
                                    "https://api.topinbiz.com/upload/images/new-face/parallex/banner-6-4-4.jpg",
                                "title" => "",
                                "text" => "",
                                "uri" => "",
                                "url" => "",
                                "button" => "",
                                "is_local" => true,
                            ],
                            [
                                "image" =>
                                    "https://api.topinbiz.com/upload/images/new-face/parallex/banner-6-4-3.jpg",
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
                        "thumbnail" => "https://api.topinbiz.com/upload/images/493x484.png",
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
                        "more_link" => "/blog/",
                        "more_title" => "مشاهده بیشتر",
                        "list" => $this->itemService->getItemList(['type' => 'blog', 'limit' => 3, 'page' => 1])['data']['list'],

                    ],
                    "testimonial" => [
                        [
                            "id" => 1,
                            "slug" => "slug1",
                            "title" => "تیتر1",
                            "thumbnail" => "https://api.topinbiz.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست1",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                        [
                            "id" => 2,
                            "slug" => "slug2",
                            "title" => "تیتر2",
                            "thumbnail" => "https://api.topinbiz.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست2",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                        [
                            "id" => 3,
                            "slug" => "slug3",
                            "title" => "تیتر3",
                            "thumbnail" => "https://api.topinbiz.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست3",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                        [
                            "id" => 4,
                            "slug" => "slug4",
                            "title" => "تیتر4",
                            "thumbnail" => "https://api.topinbiz.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست4",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                        [
                            "id" => 5,
                            "slug" => "slug5",
                            "title" => "تیتر5",
                            "thumbnail" => "https://api.topinbiz.com/upload/images/400x400.jpg",
                            "caption" => " کپشن پست5",
                            "date" => "آبان ۱۷, ۱۴۰۱",
                        ],
                    ],
                    "instagram_section" => [
                        "list" => [
                            [
                                "image" => [
                                    "src" => "https://api.topinbiz.com/upload/ver-03/insta-01.jpg?" . time(),
                                ]
                            ],
                            [
                                "image" => [
                                    "src" => "https://api.topinbiz.com/upload/ver-03/insta-02.jpg?" . time(),
                                ]
                            ],
                            [
                                "image" => [
                                    "src" => "https://api.topinbiz.com/upload/ver-03/insta-03.jpg?" . time(),
                                ]
                            ],
                            [
                                "image" => [
                                    "src" => "https://api.topinbiz.com/upload/ver-03/insta-04.jpg?" . time(),
                                ]
                            ]
                        ],
                        "type" => "product",
                        "title" => "",
                        "more_link" => "/products/",
                        "more_title" => "مشاهده بیشتر",
                        "background" => "https://api.topinbiz.com/upload/ver-03/right-side-main.png",
                        "abstract" => ""
                    ],
                ];
                break;
            case "topinbiz":
                $result = [
                    "home_sliders" => [
                        [
                            "id" => 1,
                            "image" => "https://api.topinbiz.com/upload/top-in-biz/slider01.png",
                            "title_1" => "Excellent",
                            "title_2" => "consultants",
                            "text" => "TopInBiz knows how to deal with matters in front of a company in China or Iran."
                        ],
                        [
                            "id" => 1,
                            "image" => "https://api.topinbiz.com/upload/top-in-biz/slider02.png",
                            "title_1" => "General",
                            "title_2" => "Trading",
                            "text" => "TopInBiz takes advantage of the knowledge and experience of its technical team in trading."
                        ],
                        [
                            "id" => 1,
                            "image" => "https://api.topinbiz.com/upload/top-in-biz/slider03.png",
                            "title_1" => "IT",
                            "title_2" => "Services",
                            "text" => "TopInBiz has the aim of providing services in the field of information technology with the help of the knowledge of its technical team experts."
                        ]
                    ],
                    "services" => [
                        "title" => "our services",
                        "sub_title" => "We Focused On Modern Consulting And Solutions",
                        "items" =>
                            [
                                [
                                    "id" => 1,
                                    "title" => "Legal Services",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/legal.png",
                                    "to" => "/services/legal-services"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Investment",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/investment.png",
                                    "to" => "/services/investment"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Company Registration",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/registration.png",
                                    "to" => "/services/company-registration"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Marketing & Advertisement",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/market.png",
                                    "to" => "/services/marketing-advertisement"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "General Trading",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/trading.png",
                                    "to" => "/services/general-trading"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Express Delivery",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/express.png",
                                    "to" => "/services/express-delivery"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Travel and Exhibitions",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/tour.png",
                                    "to" => "/services/travel-exhibitions"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Technology Transmission",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/tech.png",
                                    "to" => "/services/technology-transmission"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "SCO",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/sco.png",
                                    "to" => "/services/sco"
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Products",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/products.png",
                                    "to" => ""
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Supply by order",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/inventory.png",
                                    "to" => ""
                                ],
                                [
                                    "id" => 1,
                                    "title" => "Other",
                                    "image" => "https://api.topinbiz.com/upload/top-in-biz/services/other.png",
                                    "to" => "/services/other"
                                ],
                            ]
                    ],
                    "gallery"=>[
                        "title"=>"Gallery",
                        "images"=>[
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/1.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/2.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/3.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/4.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/5.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/6.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/7.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/8.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/9.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/10.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/11.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/12.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/13.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/14.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/15.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/16.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/17.png"
                            ],
                            [
                                "id"=>1,
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/gallery/18.png"
                            ],
                        ]
                    ],
                    "team"=>[
                        "title"=>"Our Team",
                        "person"=>[
                            [
                                "id"=>1,
                                "name"=>"Mohammad Rahimi",
                                "position"=>"CEO",
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/team/1.png"
                            ],
                            [
                                "id"=>1,
                                "name"=>"Mohammad Rahimi",
                                "position"=>"CEO",
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/team/2.png"
                            ],
                            [
                                "id"=>1,
                                "name"=>"Ghazal Raeghi",
                                "position"=>"CEO",
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/team/3.png"
                            ],
                            [
                                "id"=>1,
                                "name"=>"Ghazal Raeghi",
                                "position"=>"CEO",
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/team/4.png"
                            ],
                            [
                                "id"=>1,
                                "name"=>"Mohammad Rahimi",
                                "position"=>"CEO",
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/team/5.png"
                            ],
                            [
                                "id"=>1,
                                "name"=>"Mohammad Rahimi",
                                "position"=>"CEO",
                                "image"=>"https://api.topinbiz.com/upload/top-in-biz/team/6.png"
                            ],
                        ]
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
