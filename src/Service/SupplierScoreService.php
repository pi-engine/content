<?php

declare(strict_types=1);

namespace Content\Service;

use Content\Repository\SupplierScoreRepository;
use Content\Repository\SupplierScoreTypeRepository;
use function trim;

/**
 * امتیازهای چندنوعی برای تأمین‌کننده (هر کمپانی چند تایپ امتیاز دارد).
 */
class SupplierScoreService implements ServiceInterface
{
    public function __construct(
        private SupplierScoreTypeRepository $scoreTypeRepository,
        private SupplierScoreRepository $scoreRepository,
        private ItemService $itemService
    ) {
    }

    /**
     * List active score types (for form and display).
     */
    public function getScoreTypes(): array
    {
        $list = $this->scoreTypeRepository->getActiveList();
        return ['result' => true, 'data' => $list, 'error' => []];
    }

    /**
     * Add or update multi-type scores for (user, supplier). Body: supplier_id or supplier_slug, scores: { score_type_id: 1-5, ... }.
     */
    public function addOrUpdateScores(array $requestBody, array $account): array
    {
        $userId = (int) ($account['id'] ?? 0);
        if ($userId <= 0) {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'Unauthorized']];
        }
        $supplierId = $this->resolveSupplierId($requestBody);
        if ($supplierId <= 0) {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'supplier_id or supplier_slug is required']];
        }
        $scores = isset($requestBody['scores']) && is_array($requestBody['scores']) ? $requestBody['scores'] : [];
        $types = $this->scoreTypeRepository->getActiveList();
        $typeIds = array_column($types, 'id');
        $saved = [];
        foreach ($scores as $scoreTypeId => $value) {
            $scoreTypeId = (int) $scoreTypeId;
            $score = (int) $value;
            if (!in_array($scoreTypeId, $typeIds, true)) {
                continue;
            }
            if ($score < SupplierScoreRepository::SCORE_MIN || $score > SupplierScoreRepository::SCORE_MAX) {
                continue;
            }
            try {
                $row = $this->scoreRepository->addOrUpdate($supplierId, $userId, $scoreTypeId, $score);
                $saved[$scoreTypeId] = $row;
            } catch (\Throwable $e) {
                return [
                    'result' => false,
                    'data'   => [],
                    'error'  => ['message' => 'امکان ثبت امتیاز در حال حاضر وجود ندارد. لطفاً بعداً تلاش کنید.'],
                ];
            }
        }
        return ['result' => true, 'data' => ['scores' => $saved], 'error' => []];
    }

    /**
     * Get current user's scores for a supplier (for pre-fill form).
     */
    public function getMyScores(array $params, array $account): array
    {
        $userId = (int) ($account['id'] ?? 0);
        if ($userId <= 0) {
            return ['result' => true, 'data' => [], 'error' => []];
        }
        $supplierId = $this->resolveSupplierId($params);
        if ($supplierId <= 0) {
            return ['result' => true, 'data' => [], 'error' => []];
        }
        try {
            $list = $this->scoreRepository->getByUserSupplier($supplierId, $userId);
            $out = [];
            foreach ($list as $row) {
                $out[$row['score_type_id']] = $row['score'];
            }
            return ['result' => true, 'data' => $out, 'error' => []];
        } catch (\Throwable $e) {
            return ['result' => true, 'data' => [], 'error' => []];
        }
    }

    /**
     * Get score averages for multiple suppliers (for list). Returns [ supplier_id => [ score_averages => [...], overall_score => float|null ], ... ]
     *
     * @param int[] $supplierIds
     * @return array<int, array{score_averages: array, overall_score: float|null}>
     */
    public function getAveragesForSupplierIds(array $supplierIds): array
    {
        if ($supplierIds === []) {
            return [];
        }
        $types = $this->scoreTypeRepository->getActiveList();
        $typeById = [];
        foreach ($types as $t) {
            $typeById[$t['id']] = $t;
        }
        $raw = $this->scoreRepository->getAveragesForSupplierIds($supplierIds);
        $out = [];
        foreach ($supplierIds as $sid) {
            $byType = $raw[$sid] ?? [];
            $scoreAverages = [];
            $sum = 0;
            $cnt = 0;
            foreach ($types as $t) {
                $tid = $t['id'];
                $avg = $byType[$tid]['average_score'] ?? null;
                $count = $byType[$tid]['count'] ?? 0;
                $scoreAverages[] = [
                    'score_type_id' => $tid,
                    'key'           => $t['key'],
                    'title_fa'      => $t['title_fa'],
                    'title_en'      => $t['title_en'],
                    'average_score' => $avg,
                    'count'         => $count,
                ];
                if ($avg !== null) {
                    $sum += $avg;
                    $cnt++;
                }
            }
            $overall = $cnt > 0 ? round($sum / $cnt, 2) : null;
            $out[$sid] = ['score_averages' => $scoreAverages, 'overall_score' => $overall];
        }
        return $out;
    }

    /**
     * Get average scores per type for a supplier (approved only). For display on supplier detail.
     */
    public function getSupplierScoreAverages(array $params): array
    {
        $supplierId = isset($params['supplier_id']) ? (int) $params['supplier_id'] : 0;
        $supplierSlug = isset($params['supplier_slug']) ? trim((string) $params['supplier_slug']) : '';
        if ($supplierId <= 0 && $supplierSlug !== '') {
            $supplier = $this->itemService->getItem($supplierSlug, 'slug', ['type' => SupplierService::TYPE_SUPPLIER]);
            if (!empty($supplier['id'])) {
                $supplierId = (int) $supplier['id'];
            }
        }
        if ($supplierId <= 0) {
            return ['result' => true, 'data' => [], 'error' => []];
        }
        try {
            $averages = $this->scoreRepository->getAveragesBySupplier($supplierId);
            $types = $this->scoreTypeRepository->getActiveList();
            $byKey = [];
            foreach ($types as $t) {
                $tid = $t['id'];
                $byKey[$t['key']] = [
                    'score_type_id' => $tid,
                    'key'           => $t['key'],
                    'title_fa'      => $t['title_fa'],
                    'title_en'      => $t['title_en'],
                    'average_score' => $averages[$tid]['average_score'] ?? null,
                    'count'         => $averages[$tid]['count'] ?? 0,
                ];
            }
            return ['result' => true, 'data' => array_values($byKey), 'error' => []];
        } catch (\Throwable $e) {
            return ['result' => true, 'data' => [], 'error' => []];
        }
    }

    /**
     * Get supplier IDs ordered by overall score (for list sort by rating).
     *
     * @return array{supplier_ids: int[], total: int}
     */
    public function getSupplierIdsOrderedByOverallScore(int $limit, int $offset): array
    {
        try {
            return $this->scoreRepository->getSupplierIdsOrderedByOverallScore($limit, $offset);
        } catch (\Throwable $e) {
            return ['supplier_ids' => [], 'total' => 0];
        }
    }

    private function resolveSupplierId(array $params): int
    {
        $supplierId = isset($params['supplier_id']) ? (int) $params['supplier_id'] : 0;
        $supplierSlug = isset($params['supplier_slug']) ? trim((string) ($params['supplier_slug'] ?? '')) : '';
        if ($supplierId > 0) {
            $supplier = $this->itemService->getItem((string) $supplierId, 'id', ['type' => SupplierService::TYPE_SUPPLIER]);
            return !empty($supplier['id']) ? (int) $supplier['id'] : 0;
        }
        if ($supplierSlug !== '') {
            $supplier = $this->itemService->getItem($supplierSlug, 'slug', ['type' => SupplierService::TYPE_SUPPLIER]);
            return !empty($supplier['id']) ? (int) $supplier['id'] : 0;
        }
        return 0;
    }
}
