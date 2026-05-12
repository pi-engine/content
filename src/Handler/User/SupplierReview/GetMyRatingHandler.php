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

class GetMyRatingHandler implements RequestHandlerInterface
{
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected SupplierReviewService $reviewService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $account = $request->getAttribute('account', []);
        if (!is_array($account)) {
            $account = [];
        }
        $params = $request->getParsedBody();
        if (!is_array($params)) {
            $params = [];
        }
        $query = $request->getQueryParams();
        if (isset($query['supplier_id'])) {
            $params['supplier_id'] = $query['supplier_id'];
        }
        if (isset($query['supplier_slug'])) {
            $params['supplier_slug'] = $query['supplier_slug'];
        }

        try {
            $result = $this->reviewService->getMyRating($params, $account);
            return new JsonResponse($result);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'result' => true,
                'data'   => null,
                'error'  => [],
            ]);
        }
    }
}
