<?php

namespace Content\Handler\Api\Supplier;

use Content\Service\SupplierService;
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
    protected SupplierService $supplierService;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        SupplierService $supplierService
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory   = $streamFactory;
        $this->supplierService = $supplierService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }
        $id = $requestBody['id'] ?? null;
        $data = $this->supplierService->getSupplierList(['user_id'=>$id]);
        return new JsonResponse([
            'result' => true,
            'data'   => $data,
            'error'  => [],
        ]);
    }
}
