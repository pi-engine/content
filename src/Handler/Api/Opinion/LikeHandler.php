<?php

namespace Content\Handler\Api\Opinion;

use Content\Service\MetaService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LikeHandler implements RequestHandlerInterface
{
    /** @var ResponseFactoryInterface */
    protected ResponseFactoryInterface $responseFactory;

    /** @var StreamFactoryInterface */
    protected StreamFactoryInterface $streamFactory;

    /** @var MetaService */
    protected MetaService $metaService;


    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface   $streamFactory,
        MetaService              $metaService
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->metaService = $metaService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        // Get account
        $account = $request->getAttribute('account');

        // Get request body
        $requestBody = $request->getParsedBody();
        $requestBody["user_id"] = $account['id'];

        $log = [
//            "hasLog"=>true,
            "action" => "like",
            "user_id" => $account['id'],
            "item_id" => $requestBody['item_id'],
            "time_create" => time(),
        ];

        // Get list of notifications
        $result = $this->metaService->like($requestBody, $log);
        $requestBody["type"] = "question";


        // Get record
        // $result = [];

        // Set result
        $result = [
            'result' => true,
            'data' => $result,
            'error' => [],
        ];

        return new JsonResponse($result);
    }
}
