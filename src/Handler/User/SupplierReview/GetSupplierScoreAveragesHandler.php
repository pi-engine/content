<?php

namespace Content\Handler\User\SupplierReview;

use Content\Service\SupplierScoreService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class GetSupplierScoreAveragesHandler implements RequestHandlerInterface
{
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected SupplierScoreService $scoreService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getParsedBody();
        if (!is_array($params) || $params === []) {
            $params = $request->getQueryParams();
        }
        if (!is_array($params)) {
            $params = [];
        }
        $result = $this->scoreService->getSupplierScoreAverages($params);
        return new JsonResponse($result);
    }
}
