<?php

namespace Content\Handler\Admin\Meta\Value;

use Content\Service\MetaService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function is_array;
use function is_object;

class MetaValueAddHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    protected ResponseFactoryInterface $responseFactory;

    /** @var StreamFactoryInterface */
    protected StreamFactoryInterface $streamFactory;

    /** @var MetaService */
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
        $account = $request->getAttribute('account', []);
        if (!is_array($account)) {
            $account = [];
        }

        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody) && !is_object($requestBody)) {
            $requestBody = [];
        }

        $result = $this->metaService->createMetaValue($requestBody, $account);

        return new JsonResponse($result);
    }
}

