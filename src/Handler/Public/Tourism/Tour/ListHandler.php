<?php

namespace Content\Handler\Public\Tourism\Tour;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ListHandler implements RequestHandlerInterface
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
        $params['type'] = 'tour';
        $result = $this->itemService->getItemList($params, $account);
        if($requestBody['type'] =='special-tours'){
            $result['data']["middle_mode_banner"] = [
                "title" => "تورهای خاص",
                "abstract" => "تورهای خاص، به تورهایی گفته می‌شود که بر اساس علاقه‌مندی‌های خاص گردشگران طراحی شده‌اند . تورهای خاص می‌توانند تجربه‌ای فراموش‌نشدنی برای گردشگران فراهم کنند و  به گردشگران این امکان را می‌دهند تا به فعالیت‌هایی که به آن‌ها علاقه دارند بپردازند و از سفر خود لذت بیشتری ببرند. اگر به دنبال تجربه‌ای متفاوت از سفر هستید، تورهای خاص یادمان گزینه مناسبی برای شما می باشد.",
                "button_title" => "درخواست مشاوره رایگان",
                "button_link" => "/contact-us/",
                "video" => "",
                "banner" => "https://yadapi.kerloper.com/upload/images/church-gh.jpg",
                "has_video" => false
            ];
        }
        return new JsonResponse($result);
    }
}