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
                "title" => "وبلاگ",
                "abstract" => "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است، و برای شرایط فعلی تکنولوژی مورد نیاز، و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد، ",
                "button_title" => "مطالعه بیشتر",
                "button_link" => "/blog/",
                "video" => "",
                "banner" => "https://yadapi.kerloper.com/upload/images/church-gh.jpg",
                "has_video" => false
            ];
        }
        return new JsonResponse($result);
    }
}