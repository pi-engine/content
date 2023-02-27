<?php

namespace Content\Handler\Api\Question;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AddHandler implements RequestHandlerInterface
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

        $params = [
            "user_id" => $requestBody['user_id'] ?? 0,
            "title" => $requestBody['title'],
            "slug" => uniqid(),
            "status" => 1,
            "type" => 'question',
            'time_create' => time()
        ];
        $information = $params;
        $information["body"] =  new \stdClass();
        $params["information"] = json_encode($information,JSON_UNESCAPED_UNICODE);


        // Get list of notifications
        $result = $this->itemService->addQuestion($params);


        // Get record
        // $result = [];

        // Set result
        $result = [
            'result' => true,
            'data' => $result,
            'error' => [],
        ];

        return new JsonResponse($result);
    }
}
