<?php

namespace Content\Handler\Public\Supplier;

use Content\Service\SupplierService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class RegisterHandler implements RequestHandlerInterface
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

        $result = $this->supplierService->addSupplierPublic($requestBody);
        $response = [
            'result' => true,
            'data'   => $result,
            'error'  => [],
        ];
        if (isset($result['result']) && $result['result'] === false) {
            $response['result'] = false;
            $response['error']   = $result['error'] ?? ['message' => 'ثبت نام ناموفق'];
            $response['data']   = $result['data'] ?? [];
        }
        return new JsonResponse($response);
    }
}
