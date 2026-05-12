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

class EditHandler implements RequestHandlerInterface
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
        $account = $request->getAttribute('account', []);
        if (!is_array($account)) {
            $account = [];
        }
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }

        $result = $this->materialOfferService->editOffer($requestBody, $account);
        return new JsonResponse($result);
    }
}
