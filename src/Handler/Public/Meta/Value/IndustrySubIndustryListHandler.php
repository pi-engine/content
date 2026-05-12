<?php

namespace Content\Handler\Public\Meta\Value;

use Content\Service\MetaService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IndustrySubIndustryListHandler implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected MetaService $metaService;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        MetaService $metaService
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory   = $streamFactory;
        $this->metaService     = $metaService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->metaService->getIndustrySubIndustryList();
        return new JsonResponse($result);
    }
}
