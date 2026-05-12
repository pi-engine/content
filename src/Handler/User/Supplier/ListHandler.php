<?php

namespace Content\Handler\User\Supplier;

use Content\Service\SupplierService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * User panel: supplier list. Returns only active suppliers.
 * list_type is forced to 'active'; inactive and pending are not accessible.
 */
class ListHandler implements RequestHandlerInterface
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
        $params = is_array($requestBody) ? $requestBody : [];

        // User panel: only active suppliers; ignore any list_type from client
        $params['list_type'] = 'active';

        $result = $this->supplierService->getSupplierList($params);
        return new JsonResponse($result);
    }
}
