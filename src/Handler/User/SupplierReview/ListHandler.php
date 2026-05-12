<?php

namespace Content\Handler\User\SupplierReview;

use Content\Service\SupplierReviewService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class ListHandler implements RequestHandlerInterface
{
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected SupplierReviewService $reviewService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestBody = $request->getParsedBody();
        $params = is_array($requestBody) ? $requestBody : [];
        try {
            $result = $this->reviewService->getApprovedListBySupplier($params);
            return new JsonResponse($result);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'result' => true,
                'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => 50, 'page' => 1]],
                'error'  => [],
            ]);
        }
    }
}
