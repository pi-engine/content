<?php

namespace Content\Handler\User\Supplier;

use Content\Service\SupplierService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

/**
 * User panel: get single supplier. Returns data only for active suppliers;
 * inactive and pending suppliers are not accessible (404).
 */
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
        $this->streamFactory  = $streamFactory;
        $this->supplierService = $supplierService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }
        $id = $requestBody['id'] ?? null;
        $slug = $requestBody['slug'] ?? null;
        if ($id !== null) {
            $data = $this->supplierService->getSupplier($id, 'id');
        } elseif ($slug !== null && $slug !== '') {
            $data = $this->supplierService->getSupplier($slug, 'slug');
        } else {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'id or slug required'],
            ]);
        }

        if (empty($data) || (isset($data['status_kind']) && $data['status_kind'] !== 'active')) {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Supplier not found or not available'],
            ]);
        }

        return new JsonResponse([
            'result' => true,
            'data'   => $data,
            'error'  => [],
        ]);
    }
}
