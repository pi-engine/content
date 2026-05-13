<?php

namespace Content\Handler\Api\Supplier;

use Content\Service\SupplierService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Middleware\AuthenticationMiddleware;
use User\Service\AccountService;

use function is_array;

class UpdateHandler implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected SupplierService $supplierService;
    protected AccountService $accountService;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        SupplierService $supplierService,
        AccountService $accountService
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory   = $streamFactory;
        $this->supplierService = $supplierService;
        $this->accountService  = $accountService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }

        // Get authenticated user from middleware
        $user = $request->getAttribute(AuthenticationMiddleware::class);
        if (!$user || !isset($user['id'])) {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Authentication required'],
            ]);
        }

        $id = $requestBody['id'] ?? null;
        $slug = $requestBody['slug'] ?? null;
        
        if (!$id && !$slug) {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'id or slug is required'],
            ]);
        }

        // Get the supplier to verify ownership
        $existing = $id
            ? $this->supplierService->getSupplier($id, 'id')
            : $this->supplierService->getSupplier($slug, 'slug');

        if (empty($existing)) {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Supplier not found'],
            ]);
        }

        // Verify that the user is the supplier admin
        $supplierAdminUserId = $existing['admin_id'] ?? $existing['supplier_admin_user_id'] ?? null;
        if (!$supplierAdminUserId || $supplierAdminUserId != $user['id']) {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Access denied. You can only update your own supplier profile.'],
            ]);
        }

        // Use the service to update the supplier
        $result = $this->supplierService->editSupplier($requestBody, $user);
        
        return new JsonResponse($result);
    }
}
