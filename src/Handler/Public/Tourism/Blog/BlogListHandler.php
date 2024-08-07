<?php

namespace Content\Handler\Public\Tourism\Blog;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BlogListHandler implements RequestHandlerInterface
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
        $params['status'] = 1;
        $params  ['type'] = 'blog'; 
        $result = $this->itemService->getItemList($params, $account);
        if ($requestBody['type'] == 'special-blogs') {
            $result['data']["middle_mode_banner"] = [
                "title" => "وبلاگ",
                "abstract" => "در وبلاگ آژانس طبیعت گردی یادمان تلاش می کنیم شما را در جریان تحولات گردشگری ، طبیعت گردی و موضوعات مرتبط با تورهای خاص گردشگری قرار دهیم . در واقع یک طبیعت گرد حرفه ای با دانشی که قبل از سفر کسب کرده ، لذت سفر خود ر ا دو چندان میکند. به شما هم پیشنهاد میکنیم مطالب خواندنی وبلاگ یادمان را از دست ندهید.",
                "button_title" => "مطالعه سفرنامه ها",
                "button_link" => "/travelogue/",
                "video" => "",
                "banner" => "https://yadapi.kerloper.com/upload/images/church-gh.jpg",
                "has_video" => false
            ];
        }
        return new JsonResponse($result);
    }
}