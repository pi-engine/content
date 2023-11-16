<?php

namespace Content\Handler\Public\Tourism\Travelogue;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TravelogueListHandler implements RequestHandlerInterface
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
        // Get account
        $account = $request->getAttribute('account');

        // Get request body
        $requestBody = $request->getParsedBody();

        // Set record params
        $requestBody['user_id'] = $account['id'] ?? 0;
        $params = $requestBody;
        $params  ['type'] = 'travelogue';
        $result = $this->itemService->getItemList($params, $account);
        if ($requestBody['type'] == 'special-travelogues') {
            $result['data']["middle_mode_banner"] = [
                "title" => "سفرنامه",
                "abstract" => "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است، و برای شرایط فعلی تکنولوژی مورد نیاز، و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد، ",
                "button_title" => "مطالعه بیشتر",
                "button_link" => "/travelogue/",
                "video" => "",
                "banner" => "https://yadapi.kerloper.com/upload/images/church-gh.jpg",
                "has_video" => false
            ];
            $result['data']["sample"] = $result['data']['list'][0];
            ///TODO: remove connect to database in per item
            $result['data']["destination"] = [
                [
                    "id" => 1,
                    "slug" => "meta-category-bali",
                    "title" => "بالی",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-bali",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-bali'])['data']['list'],
                ],
                [
                    "id" => 2,
                    "slug" => "meta-category-brazil",
                    "title" => "برزیل",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-brazil",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-brazil'])['data']['list'],
                ],
                [
                    "id" => 3,
                    "slug" => "meta-category-peru",
                    "title" => "پرو",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-peru",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-peru'])['data']['list'],
                ],
                [
                    "id" => 4,
                    "slug" => "meta-category-thailand",
                    "title" => "تایلند",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-thailand",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-thailand'])['data']['list'],
                ],
                [
                    "id" => 5,
                    "slug" => "meta-category-srilanka",
                    "title" => "سریلانکا",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-srilanka",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-srilanka'])['data']['list'],
                ],
                [
                    "id" => 6,
                    "slug" => "meta-category-kenya",
                    "title" => "کنیا",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-kenya",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-kenya'])['data']['list'],
                ],
                [
                    "id" => 7,
                    "slug" => "meta-category-cuba",
                    "title" => "کوبا",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-cuba",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-cuba'])['data']['list'],
                ],
                [
                    "id" => 8,
                    "slug" => "meta-category-maldives",
                    "title" => "مالدیو",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-maldives",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-maldives'])['data']['list'],
                ],
                [
                    "id" => 9,
                    "slug" => "meta-category-malaysia",
                    "title" => "مالزی",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-malaysia",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-malaysia'])['data']['list'],
                ],
                [
                    "id" => 10,
                    "slug" => "meta-category-india",
                    "title" => "هند",
                    "type" => "category",
                    "icon" => "",
                    "value" => "meta-category-india",
                    "list" => $this->itemService->getItemList(['type'=>'travelogue','categories' => 'meta-category-india'])['data']['list'],
                ]
            ];
        }
        return new JsonResponse($result);
    }
}