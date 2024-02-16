<?php

namespace Content\Handler\Admin\Entity;

use Content\Service\ItemService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EntityRemoveHandler implements RequestHandlerInterface
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

        // Retrieve the raw JSON data from the request body
        $stream = $this->streamFactory->createStreamFromFile('php://input');
        $rawData = $stream->getContents();

        // Decode the raw JSON data into an associative array
        $requestBody = json_decode($rawData, true);

        // Check if decoding was successful
        if ((json_last_error() !== JSON_ERROR_NONE) || !(isset($requestBody['id']) || isset($requestBody['slug']))) {
            // JSON decoding failed
            $errorMessage = 'Invalid JSON data';
            $errorResponse = [
                'result' => false,
                'data' => null,
                'error' => $errorMessage,
            ];
            return new JsonResponse($errorResponse, 400);
        }

        $params = [];
        $type='';
        if (isset($requestBody['id'])) {
            $params['id'] = $requestBody['id'];
            $type = 'id';
        }
        if (isset($requestBody['slug'])) {
            $params['slug'] = $requestBody['slug'];
            $type = 'slug';
        }

        $data = $this->itemService->getItem($params[$type],$type,['status'=>[0,1,2,3]]);
        $data['status'] = 3;
        $params['information'] = json_encode($data);

        $result = $this->itemService->editItem($params, $account);

        // Set the response data
        $responseBody = [
            'result' => true,
            'data' => $result,
            'error' => null,
        ];

        return new JsonResponse($responseBody);
    }
}
