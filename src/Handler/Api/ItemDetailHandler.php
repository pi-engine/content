<?php

namespace Content\Handler\Api;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Content\Service\ItemService;

class ItemDetailHandler implements RequestHandlerInterface
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
        // Get request body
        $requestBody = $request->getParsedBody();

        // ToDo: Move this check to middleware
        if (empty($requestBody['slug'])) {
            // Set result
            $result = [
                'result' => false,
                'data'   => [],
                'error'  => [
                    'message' => 'Set slug !',
                ],
            ];
        } else {
            // Get list of notifications
            $result = $this->itemService->getItem($requestBody['slug'], 'slug');

            // Set result
            $result = [
                'result' => true,
                'data'   => $result,
                'error'  => [],
            ];
        }

        return new JsonResponse($result);
    }
}
