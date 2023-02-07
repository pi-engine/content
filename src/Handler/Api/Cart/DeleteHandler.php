<?php

namespace Content\Handler\Api\Cart;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DeleteHandler implements RequestHandlerInterface
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

        // Get account
        $account = $request->getAttribute('account');

        // Get request body
        $requestBody = $request->getParsedBody();
        $params["user_id"] = $account['id'];
        $params["count"] = $requestBody['count'];
        $params["type"] = "type";
        $params["product_id"] = $requestBody['id'];
        $params["slug"] = $requestBody['slug'];

        // Get list of notifications
        $result = $this->itemService->deleteCartItem($params, $account);


        // Get record
        // $result = [];

        // Set result
        $result = [
            'result' => true,
            'data'   => $result,
            'error'  => [],
        ];

        return new JsonResponse($result);
    }
}
