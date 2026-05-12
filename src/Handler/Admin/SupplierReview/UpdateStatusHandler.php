<?php

namespace Content\Handler\Admin\SupplierReview;

use Content\Service\SupplierReviewService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class UpdateStatusHandler implements RequestHandlerInterface
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
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }

        try {
            $result = $this->reviewService->updateStatus($requestBody, $account);
            return new JsonResponse($result);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'امکان بروزرسانی وضعیت در حال حاضر وجود ندارد.'],
            ]);
        }
    }
}
