<?php

namespace Content\Handler\Admin\Material;

use Content\Service\MaterialService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class AddHandler implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected MaterialService $materialService;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        MaterialService $materialService
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory   = $streamFactory;
        $this->materialService = $materialService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $account = $request->getAttribute('account', []);
        if (!is_array($account)) {
            $account = [];
        }
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }

        $result = $this->materialService->addMaterial($requestBody, $account);
        $response = [
            'result' => true,
            'data'   => $result,
            'error'  => [],
        ];
        if (isset($result['result']) && $result['result'] === false) {
            $response['result'] = false;
            $response['error'] = $result['error'] ?? ['message' => 'Add failed'];
            $response['data'] = $result['data'] ?? [];
        }
        return new JsonResponse($response);
    }
}
