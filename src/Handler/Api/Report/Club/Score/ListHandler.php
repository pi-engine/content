<?php

namespace Content\Handler\Api\Report\Club\Score;

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

        // Set record params
        $params = [
            'page' => $requestBody['page'] ?? 1,
            'limit' => $requestBody['limit'] ?? 25,
            'user_id' => $requestBody['user_id'] ?? 0,
            'role' => $requestBody['role'] ?? "customer",
            'item_id' => $requestBody['item_id'] ?? 0,
        ];


        /// TODO: move to  independent service
        $result = $this->itemService->getReportClubScoreList($params, $account);

        return new JsonResponse($result);
    }
}