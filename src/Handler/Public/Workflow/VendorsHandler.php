<?php

namespace Content\Handler\Public\Workflow;

use Content\Service\WorkflowDataService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Public endpoint for n8n workflow: vendors (suppliers) by material keys and optional industry.
 * GET /public/content/workflow/vendors?materialKey=key1,key2&industryKey=key
 */
class VendorsHandler implements RequestHandlerInterface
{
    protected WorkflowDataService $workflowDataService;

    public function __construct(WorkflowDataService $workflowDataService)
    {
        $this->workflowDataService = $workflowDataService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        $params = [
            'materialKey'  => $query['materialKey'] ?? $query['material_key'] ?? '',
            'industryKey'  => $query['industryKey'] ?? $query['industry_key'] ?? '',
        ];
        $result = $this->workflowDataService->getVendorsForWorkflow($params);
        return new JsonResponse($result);
    }
}
