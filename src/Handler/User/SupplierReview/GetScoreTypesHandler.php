<?php

namespace Content\Handler\User\SupplierReview;

use Content\Service\SupplierScoreService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetScoreTypesHandler implements RequestHandlerInterface
{
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected SupplierScoreService $scoreService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->scoreService->getScoreTypes();
        return new JsonResponse($result);
    }
}
