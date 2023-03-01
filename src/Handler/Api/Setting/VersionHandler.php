<?php

namespace Content\Handler\Api\Setting;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VersionHandler implements RequestHandlerInterface
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
        // Get request body
        $requestBody = $request->getParsedBody();

        $params["platform"] =
            isset($requestBody["platform"]) ?
                in_array($requestBody["platform"], ["client", "ios", "android"])
                    ? $requestBody["platform"] :
                    "api" :
                "api";

        $params["version"] =
            isset($requestBody["version"]) ?
                (string)$requestBody["version"] ?? "0" : "0";


        $result = $this->itemService->getVersion($params);
        $result = [
            'result' => true,
            'data' => $result,
            'error' => [],
        ];
        return new JsonResponse($result);
    }
}