<?php

namespace Content\Handler\Admin\MaterialOffer;

use Content\Service\MaterialOfferService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;
use function json_decode;
use function json_encode;
use function stream_context_create;
use function trim;

/**
 * Returns pricing context for a material: stats (other suppliers' approved offers) + AI-generated explanation.
 * POST body: material_slug. Uses current user company_id to exclude own offers from stats.
 * Calls ai-core POST /material-pricing-explanation for the explanation text.
 */
class PricingContextHandler implements RequestHandlerInterface
{
    protected MaterialOfferService $materialOfferService;
    protected string $aiCoreBaseUrl;

    public function __construct(MaterialOfferService $materialOfferService, string $aiCoreBaseUrl)
    {
        $this->materialOfferService = $materialOfferService;
        $this->aiCoreBaseUrl        = trim($aiCoreBaseUrl, '/');
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody)) {
            $requestBody = [];
        }
        $materialSlug = trim((string) ($requestBody['material_slug'] ?? ''));
        if ($materialSlug === '') {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'material_slug is required'],
            ]);
        }

        $account    = $request->getAttribute('account', []);
        $account    = is_array($account) ? $account : [];
        $companyId  = (int) ($account['company_id'] ?? $account['information']['company_id'] ?? 0);

        $statsResult = $this->materialOfferService->getPricingStatsForMaterial($materialSlug, $companyId);
        if (empty($statsResult['result']) || !isset($statsResult['data'])) {
            return new JsonResponse([
                'result' => false,
                'data'   => [],
                'error'  => $statsResult['error'] ?? ['message' => 'Failed to get pricing stats'],
            ]);
        }

        $statsData = $statsResult['data'];
        $payload   = [
            'material_title' => $statsData['material_title'] ?? '',
            'stats'          => [
                'count'            => $statsData['count'] ?? 0,
                'min_price_rial'   => $statsData['min_price_rial'] ?? 0,
                'max_price_rial'   => $statsData['max_price_rial'] ?? 0,
                'avg_price_rial'   => $statsData['avg_price_rial'] ?? 0,
                'by_country'       => $statsData['by_country'] ?? [],
            ],
        ];

        $explanation = '';
        $url        = $this->aiCoreBaseUrl . '/material-pricing-explanation';
        $ctx        = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout' => 15,
            ],
        ]);
        $responseBody = @file_get_contents($url, false, $ctx);
        if ($responseBody !== false) {
            $decoded = json_decode($responseBody, true);
            if (is_array($decoded) && isset($decoded['explanation'])) {
                $explanation = trim((string) $decoded['explanation']);
            }
        }

        return new JsonResponse([
            'result' => true,
            'data'   => [
                'stats'       => $statsData,
                'explanation' => $explanation,
            ],
            'error'  => [],
        ]);
    }
}
