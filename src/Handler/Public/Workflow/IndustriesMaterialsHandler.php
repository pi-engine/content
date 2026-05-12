<?php

namespace Content\Handler\Public\Workflow;

use Content\Service\WorkflowDataService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Public endpoint for n8n workflow: industries + sub-industries + materials.
 * GET /public/content/workflow/industries-materials
 */
class IndustriesMaterialsHandler implements RequestHandlerInterface
{
    protected WorkflowDataService $workflowDataService;

    public function __construct(WorkflowDataService $workflowDataService)
    {
        $this->workflowDataService = $workflowDataService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->workflowDataService->getIndustriesMaterialsForWorkflow();
        return new JsonResponse($result);
    }
}
