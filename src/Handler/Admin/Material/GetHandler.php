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

class GetHandler implements RequestHandlerInterface
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
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }
        $id = $requestBody['id'] ?? $requestBody['slug'] ?? null;
        $type = isset($requestBody['slug']) ? 'slug' : 'id';
        if ($id === null || $id === '') {
            return new JsonResponse(['result' => false, 'data' => [], 'error' => ['message' => 'id or slug required']]);
        }
        $item = $this->materialService->getMaterial((string) $id, $type);
        return new JsonResponse(['result' => true, 'data' => $item, 'error' => []]);
    }
}
