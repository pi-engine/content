<?php

namespace Content\Handler\Admin\Supplier;

use Content\Service\SupplierService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class EditHandler implements RequestHandlerInterface
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
        $account = $request->getAttribute('account', []);
        if (!is_array($account)) {
            $account = [];
        }
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }

        $result = $this->supplierService->editSupplier($requestBody, $account);
        if (isset($result['result']) && $result['result'] === false) {
            return new JsonResponse([
                'result' => false,
                'data'   => $result['data'] ?? [],
                'error'  => $result['error'] ?? ['message' => 'Update failed'],
            ]);
        }
        return new JsonResponse([
            'result' => true,
            'data'   => $result,
            'error'  => [],
        ]);
    }
}
