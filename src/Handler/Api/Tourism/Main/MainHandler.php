<?php

namespace Content\Handler\Api\Tourism\Main;

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
        StreamFactoryInterface   $streamFactory,
        ItemService              $itemService
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->itemService = $itemService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $list = [
            ['title' => 'تورهای داخلی',
                'slug' => "inner_tour", 'child' => [
                [
                    "name" => "آذربایجان غربی",
                    "slug" => "west_azarbaijan",
                    "child" => [
                        [
                            "name" => "شلماش تا گراوان",
                            "slug" => "shelmash_to_garavan"
                        ],
                        [
                            "name" => "قره کلیسا تا دالامپر",
                            "slug" => "qareh_kelisa_to_dalamper"
                        ],
                        [
                            "name" => "گراوان تا تخت سلیمان",
                            "slug" => "garavan_to_takht_soleiman"
                        ]
                    ]
                ],
                [
                    "name" => "اردبیل",
                    "slug" => "ardabil",
                    "child" => [
                        [
                            "name" => "خلخال تا اسالم",
                            "slug" => "khalkhal_to_eslam"
                        ],
                        [
                            "name" => "سرعین سوها تا آبشار لاتون",
                            "slug" => "sarein_souha_to_laton_waterfall"
                        ],
                        [
                            "name" => "شروان دره تا جهنم دره",
                            "slug" => "shorvan_darre_to_jahannam_darre"
                        ],
                        [
                            "name" => "صعود سبلان",
                            "slug" => "sabalan_climbing"
                        ]
                    ]
                ],
                [
                    "name" => "تهران",
                    "slug" => "tehran",
                    "child" => [
                        [
                            "name" => "آهار به شکرآب",
                            "slug" => "ahar_to_shekar_ab"
                        ],
                        [
                            "name" => "امامه به کلوگان",
                            "slug" => "emameh_to_kolougan"
                        ],
                        [
                            "name" => "تنگه واشی تهران گردی",
                            "slug" => "vashi_strait_tehran_tour"
                        ],
                        [
                            "name" => "تهران گردی ویژه مهمانان خارجی",
                            "slug" => "tehran_tour_foreign_guests"
                        ],
                        [
                            "name" => "دره زمان",
                            "slug" => "darreh_zaman"
                        ],
                        [
                            "name" => "روستای هرانده تا خمده",
                            "slug" => "herandeh_village_to_khomede"
                        ],
                        [
                            "name" => "شقایق دشت لار",
                            "slug" => "sheqaqeh_dasht_lar"
                        ],
                        [
                            "name" => "غار رود افشان",
                            "slug" => "roud_afshan_cave"
                        ],
                        [
                            "name" => "میشینه",
                            "slug" => "mishehneh"
                        ],
                        [
                            "name" => "مرگ",
                            "slug" => "marg"
                        ]
                    ]
                ],
                [
                    "name" => "چهارمحال و بختیاری",
                    "slug" => "chaharmahal_and_bakhtiari",
                    "child" => [
                        [
                            "name" => "آبشار شیوند",
                            "slug" => "shivan_waterfall"
                        ],
                        [
                            "name" => "دیار بختیاری",
                            "slug" => "diar_bakhtiari"
                        ],
                        [
                            "name" => "رفتینگ در رودخانه کوهرنگ و لاله",
                            "slug" => "kouhrang_river_rafting_laleh"
                        ],
                        [
                            "name" => "واژگون",
                            "slug" => "vazegoon"
                        ]
                    ]
                ],
                [
                    "name" => "خراسان",
                    "slug" => "khorasan",
                    "child" => [
                        [
                            "name" => "خواف و نشتیفان",
                            "slug" => "khaf_neshatian"
                        ],
                        [
                            "name" => "دره شمخال تا آبشار قره‌سو",
                            "slug" => "shamkhal_to_qareh_soo_waterfall"
                        ],
                        [
                            "name" => "مشهد",
                            "slug" => "mashhad"
                        ]
                    ]
                ],
                [
                    "name" => "خراسان جنوبی",
                    "slug" => "south_khorasan",
                    "child" => [
                        [
                            "name" => "طبس",
                            "slug" => "tabas"
                        ]
                    ]
                ],
                [
                    "name" => "خوزستان",
                    "slug" => "khuzestan",
                    "child" => [
                        [
                            "name" => "آبشار شوی اهواز",
                            "slug" => "ahvaz_shavi_waterfall"
                        ],
                        [
                            "name" => "خوزستان لرستان و خوزستان",
                            "slug" => "khuzestan_lorstan_and_khuzestan"
                        ]
                    ]
                ],
                [
                    "name" => "زنجان",
                    "slug" => "zanjan",
                    "child" => [
                        [
                            "name" => "زنجان‌ گردی",
                            "slug" => "zanjan_tour"
                        ],
                        [
                            "name" => "غار کتله خور تا خرقان",
                            "slug" => "katleh_khor_to_khorqan_cave"
                        ]
                    ]
                ],
                [
                    "name" => "سمنان",
                    "slug" => "semnan",
                    "child" => [
                        [
                            "name" => "تپه مریخی و پاده",
                            "slug" => "marikh_hill_and_padeh"
                        ],
                        [
                            "name" => "جنگل ابر",
                            "slug" => "cloud_forest"
                        ],
                        [
                            "name" => "دریاچه نمک حاج علی",
                            "slug" => "haj_ali_salt_lake"
                        ],
                        [
                            "name" => "قلعه بالا و رضاآباد",
                            "slug" => "ghaleh_bala_rezaabad"
                        ],
                        [
                            "name" => "کالپوش تا آبشار لوه",
                            "slug" => "kalpoosh_to_loo_waterfall"
                        ]
                    ]
                ],
                [
                    "name" => "سیستان و بلوچستان",
                    "slug" => "sistan_and_baluchestan",
                    "child" => [
                        [
                            "name" => "قله تفتان",
                            "slug" => "taftan_peak"
                        ],
                        [
                            "name" => "چابهار",
                            "slug" => "chabahar"
                        ],
                        [
                            "name" => "کویر لوت تا شهر سوخته",
                            "slug" => "loot_desert_to_burnt_city"
                        ]
                    ]
                ],
                [
                    "name" => "فارس",
                    "slug" => "fars",
                    "child" => [
                        [
                            "name" => "بیشاپور تا بوشهر",
                            "slug" => "bishapour_to_boushehr"
                        ],
                        [
                            "name" => "تنگ گمبیل",
                            "slug" => "gambil_strait"
                        ],
                        [
                            "name" => "تنگه رغز",
                            "slug" => "raghaz_strait"
                        ],
                        [
                            "name" => "شیراز",
                            "slug" => "shiraz"
                        ]
                    ]
                ],
                [
                    "name" => "قزوین",
                    "slug" => "qazvin",
                    "child" => [
                        [
                            "name" => "الموت غار کتله خور تا خرقان",
                            "slug" => "almut_katleh_khor_to_khorqan_cave"
                        ],
                        [
                            "name" => "قزوین گردی",
                            "slug" => "qazvin_tour"
                        ]
                    ]
                ],
                [
                    "name" => "قم",
                    "slug" => "qom",
                    "child" => [
                        [
                            "name" => "تنگه قاهان",
                            "slug" => "qahan_strait"
                        ],
                        [
                            "name" => "تپه‌ نمکی طغرود تا حوض‌سلطان",
                            "slug" => "taghrood_salt_hill_to_hoz_soltan"
                        ]
                    ]
                ],
                [
                    "name" => "کردستان",
                    "slug" => "kurdistan",
                    "child" => [
                        [
                            "name" => "مریوان و سنندج",
                            "slug" => "marivan_and_sannadaj"
                        ],
                        [
                            "name" => "کردستان",
                            "slug" => "kurdistan"
                        ]
                    ]
                ],
                [
                    "name" => "کرمان",
                    "slug" => "kerman",
                    "child" => [
                        [
                            "name" => "بارش شهابی تورهای نجومی",
                            "slug" => "meteor_shower_astronomical_tours"
                        ],
                        [
                            "name" => "دره راگه تا روستای میمند",
                            "slug" => "ragheh_valley_to_mimand_village"
                        ],
                        [
                            "name" => "کرمان گردی",
                            "slug" => "kerman_tour"
                        ],
                        [
                            "name" => "کلوت و کرمان",
                            "slug" => "kelout_to_kerman"
                        ]
                    ]
                ],
                [
                    "name" => "کرمانشاه",
                    "slug" => "kermanshah",
                    "child" => [
                        [
                            "name" => "دامنه های دالاهو",
                            "slug" => "dalahoo_peaks"
                        ],
                        [
                            "name" => "رقص و موسیقی کرمانشاه",
                            "slug" => "kermanshah_dance_and_music"
                        ]
                    ]
                ],
                [
                    "name" => "کهکیلویه و بویراحمد",
                    "slug" => "kohgiluyeh_and_boyer-ahmad",
                    "child" => [
                        [
                            "name" => "دنا و سی سخت",
                            "slug" => "dena_and_see_sakht"
                        ]
                    ]
                ],
                [
                    "name" => "گلستان",
                    "slug" => "golestan",
                    "child" => [
                        [
                            "name" => "آبشارهای گلستان",
                            "slug" => "golestan_waterfalls"
                        ],
                        [
                            "name" => "باران کوه تا شیرآباد",
                            "slug" => "baran_kooh_to_shirabad"
                        ],
                        [
                            "name" => "ترکمن صحرا",
                            "slug" => "turkmen_desert"
                        ],
                        [
                            "name" => "جهان نما پارک ملی گلستان",
                            "slug" => "jahannama_national_park"
                        ],
                        [
                            "name" => "گرگان",
                            "slug" => "gorgan"
                        ]
                    ]
                ],
                [
                    "name" => "گیلان",
                    "slug" => "gilan",
                    "child" => [
                        [
                            "name" => "آبشار روخانکول",
                            "slug" => "rokhankol_waterfall"
                        ],
                        [
                            "name" => "آبشار سنگان",
                            "slug" => "sangan_waterfall"
                        ],
                        [
                            "name" => "جنگل نقله بر رودبار",
                            "slug" => "noghlebar_forest"
                        ],
                        [
                            "name" => "جنگل نور چشمه تا غار دیورش",
                            "slug" => "noor_cheshme_to_divoresh_cave"
                        ],
                        [
                            "name" => "خلخال تا اسالم",
                            "slug" => "khal_khal_to_eslam"
                        ],
                        [
                            "name" => "دریاچه عروس",
                            "slug" => "aroush_lake"
                        ],
                        [
                            "name" => "رامسر و شمال گردی",
                            "slug" => "ramsar_and_northern_tour"
                        ],
                        [
                            "name" => "رودخانه‌نوردی",
                            "slug" => "river_riding"
                        ],
                        [
                            "name" => "تنگ دار سوباتان",
                            "slug" => "tang_dar_sobatan"
                        ],
                        [
                            "name" => "قلعه رودخان ماسال",
                            "slug" => "masal_rudkhan_castle"
                        ],
                        [
                            "name" => "ماسوله",
                            "slug" => "masouleh"
                        ],
                        [
                            "name" => "پارک ملی بوجاق و تالاب نیلوفر",
                            "slug" => "bojagh_neloufar_wetland_national_park"
                        ],
                        [
                            "name" => "گیلده ییلاق جواهردشت",
                            "slug" => "gildeh_javaher_dasht"
                        ],
                        [
                            "name" => "ییلاق داماش",
                            "slug" => "yilagh_damash"
                        ],
                        [
                            "name" => "ییلاق سی‌ دشت",
                            "slug" => "yilagh_si_dasht"
                        ],
                        [
                            "name" => "ییلاق هلودشت تا میلاش",
                            "slug" => "helodush_to_milash"
                        ],
                        [
                            "name" => "ییلاق یسان",
                            "slug" => "yilagh_yasan"
                        ],
                        [
                            "name" => "ییلاقات دیلمان و درفک",
                            "slug" => "dilman_and_darvak_highlands"
                        ]
                    ]
                ],
                [
                    "name" => "لرستان",
                    "slug" => "lorestan",
                    "child" => [
                        [
                            "name" => "تنگه شیرز تا دره خزینه",
                            "slug" => "shiraz_strait_to_khazineh_valley"
                        ],
                        [
                            "name" => "دریاچه گهر لرستان",
                            "slug" => "gahar_lake"
                        ],
                        [
                            "name" => "لرستان و خوزستان",
                            "slug" => "lorestan_and_khuzestan"
                        ]
                    ]
                ],
                [
                    "name" => "مازندران",
                    "slug" => "mazandaran",
                    "child" => [
                        [
                            "name" => "آبشار آهکی",
                            "slug" => "ahaki_waterfall"
                        ],
                        [
                            "name" => "آبشار اسپه او",
                            "slug" => "asp-e_o_waterfall"
                        ],
                        [
                            "name" => "آبشار بولا",
                            "slug" => "bola_waterfall"
                        ],
                        [
                            "name" => "آبشار ترز",
                            "slug" => "taraz_waterfall"
                        ],
                        [
                            "name" => "آبشار جلسنگ و اسپی",
                            "slug" => "jalsang_esp_waterfall"
                        ],
                        [
                            "name" => "آر آبشار سمبی",
                            "slug" => "ar_sambi_waterfall"
                        ],
                        [
                            "name" => "آبشار سوته راش",
                            "slug" => "suteh_rash_waterfall"
                        ],
                        [
                            "name" => "آبشار عروس",
                            "slug" => "arous_waterfall"
                        ],
                        [
                            "name" => "دامن آبشار هریجان",
                            "slug" => "daman_harizan_waterfall"
                        ],
                        [
                            "name" => "آبشار گزو",
                            "slug" => "gozow_waterfall"
                        ],
                        [
                            "name" => "ارفع‌ ده اوروست تا چناربن",
                            "slug" => "orfo_dasht_to_chenarban"
                        ],
                        [
                            "name" => "باداب سورت",
                            "slug" => "badab_sort"
                        ],
                        [
                            "name" => "جنگل الیمستان",
                            "slug" => "elimestan_forest"
                        ],
                        [
                            "name" => "جنگل راش",
                            "slug" => "rasht_forest"
                        ],
                        [
                            "name" => "جنگل‌ های انجیلی",
                            "slug" => "angelic_forests"
                        ],
                        [
                            "name" => "جواهرده",
                            "slug" => "javaherdeh"
                        ],
                        [
                            "name" => "دریاچه آویدر",
                            "slug" => "avider_lake"
                        ],
                        [
                            "name" => "دریاچه ارواح",
                            "slug" => "arvah_lake"
                        ],
                        [
                            "name" => "دریاچه فراخین تا آبشار دارنو",
                            "slug" => "farakhin_lake_to_darunow_waterfall"
                        ],
                        [
                            "name" => "دشت دریاسر",
                            "slug" => "daryasar_plain"
                        ],
                        [
                            "name" => "دشت نمارستاق تا آبشار دریوک",
                            "slug" => "namarestagh_plain_to_daryuk_waterfall"
                        ],
                        [
                            "name" => "رامسر و شمال گردی",
                            "slug" => "ramsar_and_northern_tour"
                        ],
                        [
                            "name" => "رفتینگ در رودخانه",
                            "slug" => "river_rafting"
                        ],
                        [
                            "name" => "رودخانه‌نوردی",
                            "slug" => "river_exploration"
                        ],
                        [
                            "name" => "اسکلیم رود سرپوش",
                            "slug" => "rood_serposh_watercourse"
                        ],
                        [
                            "name" => "تنگه شاهان",
                            "slug" => "shahan_strait"
                        ],
                        [
                            "name" => "دشت صعود دماوند",
                            "slug" => "damavand_ascent_plain"
                        ],
                        [
                            "name" => "علم کوه قطار شمال (گردشگری)",
                            "slug" => "alam_kouh_qatar_north_tourism"
                        ],
                        [
                            "name" => "قلعه کنگلو تا اسپهبد خورشید",
                            "slug" => "kongloo_castle_to_khorshid_esph"
                        ],
                        [
                            "name" => "لفور تا هفت آبشار",
                            "slug" => "lafor_to_haft_abshar"
                        ],
                        [
                            "name" => "مازیچال",
                            "slug" => "mazichal"
                        ],
                        [
                            "name" => "مرداب دیوک",
                            "slug" => "divuk_marsh"
                        ],
                        [
                            "name" => "مرداب هسل",
                            "slug" => "hasel_wetland"
                        ],
                        [
                            "name" => "میانکاله نوا تا دشت آزو",
                            "slug" => "miyankaleh_to_dasht_azu"
                        ],
                        [
                            "name" => "پل ورسک تا دریاچه شورمست",
                            "slug" => "pol_varesk_to_daryache_shurmast"
                        ],
                        [
                            "name" => "پلنگ دره",
                            "slug" => "pilang_darreh"
                        ],
                        [
                            "name" => "کمپینگ کندلوس یوش و بلده",
                            "slug" => "camping_kandelous_yosh_baladeh"
                        ],
                        [
                            "name" => "ییلاق آغوزحال",
                            "slug" => "yilagh_aghozhal"
                        ],
                        [
                            "name" => "ییلاق الیت و دلیر",
                            "slug" => "yilagh_elit_delir"
                        ],
                        [
                            "name" => "ییلاق سلانسر",
                            "slug" => "yilagh_salansar"
                        ],
                        [
                            "name" => "ییلاق فیلبند",
                            "slug" => "yilagh_filband"
                        ]
                    ]
                ],
                [
                    "name" => "مرکزی",
                    "slug" => "markazi",
                    "child" => [
                        [
                            "name" => "تالاب میقان",
                            "slug" => "miqan_wetland"
                        ],
                        [
                            "name" => "غار کهک",
                            "slug" => "kahak_cave"
                        ]
                    ]
                ],
                [
                    "name" => "هرمزگان",
                    "slug" => "hormozgan",
                    "child" => [
                        [
                            "name" => "جزیره هرمز",
                            "slug" => "hormuz_island"
                        ],
                        [
                            "name" => "قشم",
                            "slug" => "qeshm"
                        ],
                        [
                            "name" => "کیش",
                            "slug" => "kish"
                        ]
                    ]
                ],
                [
                    "name" => "همدان",
                    "slug" => "hamedan",
                    "child" => [
                        [
                            "name" => "غار آبی علیصدر",
                            "slug" => "abi_ali_sadr_cave"
                        ],
                        [
                            "name" => "ورکانه و تویسرکان",
                            "slug" => "varzaneh_and_tuyserkan"
                        ]
                    ]
                ],
                [
                    "name" => "یزد",
                    "slug" => "yazd",
                    "child" => [
                        [
                            "name" => "جشن سده ریگ زرین و خرانق",
                            "slug" => "jashn_sadeh_rig_zarin_kharanaq"
                        ],
                        [
                            "name" => "عقدا کویر",
                            "slug" => "aghda_kavir"
                        ],
                        [
                            "name" => "کاراکال یزد",
                            "slug" => "karakal_yazd"
                        ]
                    ]
                ]
            ],],
            ['title' => 'تورهای خارجی',
                'slug' => "foreign_tour", 'child' => [
                [
                    "name" => "آسیا",
                    "slug" => "asia",
                    "child" => [
                        [
                            "name" => "ازبکستان",
                            "slug" => "uzbekistan"
                        ],
                        [
                            "name" => "اندونزی",
                            "slug" => "indonesia"
                        ],
                        [
                            "name" => "ایندوچاینا",
                            "slug" => "indochina"
                        ],
                        [
                            "name" => "بوتان",
                            "slug" => "bhutan"
                        ],
                        [
                            "name" => "بورنئو",
                            "slug" => "borneo"
                        ],
                        [
                            "name" => "تاجیکستان",
                            "slug" => "tajikistan"
                        ],
                        [
                            "name" => "تایلند",
                            "slug" => "thailand"
                        ],
                        [
                            "name" => "تبت",
                            "slug" => "tibet"
                        ],
                        [
                            "name" => "تور_ویتنام",
                            "slug" => "vietnam_tour"
                        ],
                        [
                            "name" => "تور_کشمیر",
                            "slug" => "kashmir_tour"
                        ],
                        [
                            "name" => "سریلانکا",
                            "slug" => "sri_lanka"
                        ],
                        [
                            "name" => "عمان",
                            "slug" => "oman"
                        ],
                        [
                            "name" => "فیلیپین",
                            "slug" => "philippines"
                        ],
                        [
                            "name" => "قرقیزستان",
                            "slug" => "kyrgyzstan"
                        ],
                        [
                            "name" => "قزاقستان",
                            "slug" => "kazakhstan"
                        ],
                        [
                            "name" => "لائوس",
                            "slug" => "laos"
                        ],
                        [
                            "name" => "لادخ",
                            "slug" => "ladakh"
                        ],
                        [
                            "name" => "مالدیو",
                            "slug" => "maldives"
                        ],
                        [
                            "name" => "مالزی",
                            "slug" => "malaysia"
                        ],
                        [
                            "name" => "مغولستان",
                            "slug" => "mongolia"
                        ],
                        [
                            "name" => "نپال",
                            "slug" => "nepal"
                        ],
                        [
                            "name" => "هند",
                            "slug" => "india"
                        ],
                        [
                            "name" => "چین",
                            "slug" => "china"
                        ],
                        [
                            "name" => "ژاپن",
                            "slug" => "japan"
                        ],
                        [
                            "name" => "کامبوج",
                            "slug" => "cambodia"
                        ]
                    ]
                ],
                [
                    "name" => "آفریقا",
                    "slug" => "africa",
                    "child" => [
                        [
                            "name" => "تانزانیا",
                            "slug" => "tanzania"
                        ],
                        [
                            "name" => "تونس",
                            "slug" => "tunisia"
                        ],
                        [
                            "name" => "جزیره_موریس",
                            "slug" => "mauritius"
                        ],
                        [
                            "name" => "زنگبار",
                            "slug" => "zanzibar"
                        ],
                        [
                            "name" => "زیمباوه",
                            "slug" => "zimbabwe"
                        ],
                        [
                            "name" => "سیشل",
                            "slug" => "seychelles"
                        ],
                        [
                            "name" => "ماداگاسکار",
                            "slug" => "madagascar"
                        ],
                        [
                            "name" => "مراکش",
                            "slug" => "morocco"
                        ],
                        [
                            "name" => "کلیمانجارو",
                            "slug" => "kilimanjaro"
                        ],
                        [
                            "name" => "کنیا",
                            "slug" => "kenya"
                        ]
                    ]
                ],
                [
                    "name" => "آمریکای_جنوبی",
                    "slug" => "south_america",
                    "child" => [
                        [
                            "name" => "آلاسکا",
                            "slug" => "alaska"
                        ],
                        [
                            "name" => "اکوادور",
                            "slug" => "ecuador"
                        ],
                        [
                            "name" => "تور_برزیل",
                            "slug" => "brazil_tour"
                        ],
                        [
                            "name" => "مکزیک",
                            "slug" => "mexico"
                        ],
                        [
                            "name" => "پرو",
                            "slug" => "peru"
                        ],
                        [
                            "name" => "کوبا",
                            "slug" => "cuba"
                        ]
                    ]
                ],
                [
                    "name" => "اروپا",
                    "slug" => "europe",
                    "child" => [
                        [
                            "name" => "آنتالیا",
                            "slug" => "antalya"
                        ],
                        [
                            "name" => "اتریش",
                            "slug" => "austria"
                        ],
                        [
                            "name" => "ارمنستان",
                            "slug" => "armenia"
                        ],
                        [
                            "name" => "استانبول",
                            "slug" => "istanbul"
                        ],
                        [
                            "name" => "اسپانیا",
                            "slug" => "spain"
                        ],
                        [
                            "name" => "ایتالیا",
                            "slug" => "italy"
                        ],
                        [
                            "name" => "باکو",
                            "slug" => "baku"
                        ],
                        [
                            "name" => "ترابزون",
                            "slug" => "trabzon"
                        ],
                        [
                            "name" => "ترکیه",
                            "slug" => "turkey"
                        ],
                        [
                            "name" => "تور_گرجستان",
                            "slug" => "georgia_tour"
                        ],
                        [
                            "name" => "دور_اروپا",
                            "slug" => "europe_tour"
                        ],
                        [
                            "name" => "روسیه",
                            "slug" => "russia"
                        ],
                        [
                            "name" => "سوئیس",
                            "slug" => "switzerland"
                        ],
                        [
                            "name" => "شفق_قطبی",
                            "slug" => "polar_lights"
                        ],
                        [
                            "name" => "صربستان",
                            "slug" => "serbia"
                        ],
                        [
                            "name" => "فرانسه",
                            "slug" => "france"
                        ],
                        [
                            "name" => "قونیه",
                            "slug" => "konya"
                        ],
                        [
                            "name" => "نمایشگاه_استانبول",
                            "slug" => "istanbul_exhibition"
                        ],
                        [
                            "name" => "کاپادوکیا",
                            "slug" => "cappadocia"
                        ]
                    ]
                ]
            ],]
        ];
        $result = ["list" => $list];
        // Set result
        $result = [
            'result' => true,
            'data' => $result,
            'error' => [],
        ];

        return new JsonResponse($result);
    }
}
