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

class GetMyScoresHandler implements RequestHandlerInterface
{
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected SupplierScoreService $scoreService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $account = $request->getAttribute('account', []);
        if (!is_array($account)) {
            $account = [];
        }
        $params = $request->getParsedBody();
        if (!is_array($params) || $params === []) {
            $params = $request->getQueryParams();
        }
        if (!is_array($params)) {
            $params = [];
        }
        $result = $this->scoreService->getMyScores($params, $account);
        return new JsonResponse($result);
    }
}
