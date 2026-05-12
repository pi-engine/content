<?php

namespace Content\Handler\Admin\MaterialOffer;

use Content\Service\MaterialOfferService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;

class ListHandler implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected MaterialOfferService $materialOfferService;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        MaterialOfferService $materialOfferService
    ) {
        $this->responseFactory      = $responseFactory;
        $this->streamFactory        = $streamFactory;
        $this->materialOfferService = $materialOfferService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }

        $materialSlug = isset($requestBody['material_slug']) ? trim((string) $requestBody['material_slug']) : '';
        if ($materialSlug !== '') {
            $result = $this->materialOfferService->getOfferList($requestBody);
            return new JsonResponse($result);
        }

        $companyId = isset($requestBody['company_id']) ? (int) $requestBody['company_id'] : 0;
        $result    = $companyId > 0
            ? $this->materialOfferService->getOfferListBySupplier($requestBody)
            : $this->materialOfferService->getOfferListAll($requestBody);
        return new JsonResponse($result);
    }
}
