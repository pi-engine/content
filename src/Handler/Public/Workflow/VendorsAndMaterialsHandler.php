<?php

namespace Content\Handler\Public\Workflow;

use Content\Service\WorkflowDataService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Public endpoint: تأمین‌کنندگان + صنایع/مواد در یک پاسخ (برای ورکفلو).
 * GET با query یا POST با body: materialKey, industryKey
 */
class VendorsAndMaterialsHandler implements RequestHandlerInterface
{
    protected WorkflowDataService $workflowDataService;

    public function __construct(WorkflowDataService $workflowDataService)
    {
        $this->workflowDataService = $workflowDataService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $query = $request->getQueryParams();
        $body = is_array($body) ? $body : [];
        $params = [
            'materialKey'  => $body['materialKey'] ?? $body['material_key'] ?? $query['materialKey'] ?? $query['material_key'] ?? '',
            'industryKey'  => $body['industryKey'] ?? $body['industry_key'] ?? $query['industryKey'] ?? $query['industry_key'] ?? '',
        ];

        $vendorsResult = $this->workflowDataService->getVendorsForWorkflow($params);
        $materialsResult = $this->workflowDataService->getIndustriesMaterialsForWorkflow();

        $matches = $vendorsResult['data']['matches'] ?? [];
        $industries_materials = $materialsResult['data']['industries_materials'] ?? [];

        return new JsonResponse([
            'result' => true,
            'data'   => [
                'matches'             => $matches,
                'industries_materials' => $industries_materials,
            ],
            'error'  => [],
        ]);
    }
}
