<?php

namespace Content\Service;

use function is_array;
use function json_decode;
use function trim;

/**
 * Service for workflow (n8n) data: industries+subindustries+materials and vendors.
 * Used by public workflow endpoints so n8n can fetch data from backend.
 */
class WorkflowDataService implements ServiceInterface
{
    private const INDUSTRY_SLUG_PREFIX = 'meta-industry-';

    protected MetaService $metaService;
    protected MaterialService $materialService;
    protected MaterialOfferService $materialOfferService;
    protected SupplierService $supplierService;

    public function __construct(
        MetaService $metaService,
        MaterialService $materialService,
        MaterialOfferService $materialOfferService,
        SupplierService $supplierService
    ) {
        $this->metaService         = $metaService;
        $this->materialService     = $materialService;
        $this->materialOfferService = $materialOfferService;
        $this->supplierService     = $supplierService;
    }

    /**
     * Extract industry key from slug (e.g. meta-industry-pharmaceutical -> pharmaceutical).
     */
    private function industryKeyFromSlug(string $slug): string
    {
        if ($slug === '' || strpos($slug, self::INDUSTRY_SLUG_PREFIX) !== 0) {
            return $slug;
        }
        return (string) substr($slug, strlen(self::INDUSTRY_SLUG_PREFIX));
    }

    /**
     * For sub-industry slug (e.g. meta-industry-pharmaceutical-cosmetic_and_hygienic)
     * return [ parentKey, subKey ]. For top-level industry slug return [ key, null ].
     */
    private function industrySubKeyFromSlug(string $slug): array
    {
        $rest = $this->industryKeyFromSlug($slug);
        if ($rest === '') {
            return ['', null];
        }
        $pos = strpos($rest, '-');
        if ($pos === false) {
            return [$rest, null];
        }
        return [
            (string) substr($rest, 0, $pos),
            (string) substr($rest, $pos + 1),
        ];
    }

    /**
     * Return industries with subcategories and materials for n8n (MASTER_DATA shape).
     * GET /public/content/workflow/industries-materials
     *
     * @return array{result: bool, data: array{industries_materials: array}, error: array}
     */
    public function getIndustriesMaterialsForWorkflow(): array
    {
        $listResult = $this->metaService->getIndustrySubIndustryList();
        if (empty($listResult['result']) || empty($listResult['data'])) {
            return [
                'result' => true,
                'data'   => ['industries_materials' => []],
                'error'  => [],
            ];
        }

        $industries  = $listResult['data']['industry'] ?? [];
        $subIndustries = $listResult['data']['sub_industry'] ?? [];

        $industryByKey = [];
        foreach ($industries as $row) {
            $key = $this->industryKeyFromSlug((string) ($row['slug'] ?? ''));
            if ($key !== '') {
                $industryByKey[$key] = [
                    'key'   => $key,
                    'title' => trim((string) ($row['title'] ?? '')),
                    'subcategories' => [],
                ];
            }
        }

        $subByKey = [];
        foreach ($subIndustries as $row) {
            [$parentKey, $subKey] = $this->industrySubKeyFromSlug((string) ($row['slug'] ?? ''));
            if ($subKey === null || $subKey === '') {
                continue;
            }
            $subByKey[$subKey] = [
                'key'       => $subKey,
                'title'     => trim((string) ($row['title'] ?? '')),
                'parent_key' => $parentKey,
            ];
        }

        $materialsResult = $this->materialService->getMaterialList([
            'status' => 1,
            'limit'  => 2000,
        ]);
        $materialList = $materialsResult['data']['list'] ?? [];

        foreach ($materialList as $mat) {
            $subKeys = $mat['sub_industries_keys'] ?? [];
            if (!is_array($subKeys)) {
                continue;
            }
            $name  = trim((string) ($mat['name'] ?? $mat['title'] ?? $mat['slug'] ?? ''));
            $title = trim((string) ($mat['title'] ?? $name));
            $key   = trim((string) ($mat['key'] ?? $mat['slug'] ?? ''));
            $value = trim((string) ($mat['value'] ?? ''));
            $m = [
                'name'  => $name !== '' ? $name : $key,
                'title' => $title,
                'key'   => $key,
                'value' => $value,
            ];
            foreach ($subKeys as $sk) {
                $sk = trim((string) $sk);
                if ($sk === '') {
                    continue;
                }
                if (!isset($subByKey[$sk])) {
                    $subByKey[$sk] = ['key' => $sk, 'title' => $sk, 'parent_key' => '', 'materials' => []];
                }
                if (!isset($subByKey[$sk]['materials'])) {
                    $subByKey[$sk]['materials'] = [];
                }
                $subByKey[$sk]['materials'][] = $m;
            }
        }

        foreach ($subByKey as $subKey => $sub) {
            $parentKey = $sub['parent_key'] ?? '';
            $materials = $sub['materials'] ?? [];
            unset($sub['parent_key'], $sub['materials']);
            $sub['materials'] = $materials;
            if ($parentKey !== '' && isset($industryByKey[$parentKey])) {
                $industryByKey[$parentKey]['subcategories'][] = $sub;
            }
        }

        $industries_materials = array_values($industryByKey);
        return [
            'result' => true,
            'data'   => ['industries_materials' => $industries_materials],
            'error'  => [],
        ];
    }

    /**
     * Return vendors with full offer details per material (قیمت، میزان، واحد، کشور سازنده، ...).
     * Each match = یک تأمین‌کننده + یک offer برای یک ماده (با مشخصات کامل offer).
     *
     * @param array $params materialKey (comma-separated), industryKey (optional)
     * @return array{result: bool, data: array{matches: array}, error: array}
     */
    public function getVendorsForWorkflow(array $params): array
    {
        $materialKeyStr = isset($params['materialKey']) ? trim((string) $params['materialKey']) : '';
        $industryKey    = isset($params['industryKey']) ? trim((string) $params['industryKey']) : '';

        $materialIds       = [];
        $materialIdToKey   = [];
        $offerList         = [];
        $companyIds        = [];

        if ($materialKeyStr !== '') {
            $materialKeys = array_values(array_filter(array_map('trim', explode(',', $materialKeyStr))));
            foreach ($materialKeys as $mk) {
                $mat = $this->materialService->getMaterial($mk, 'slug');
                if (!empty($mat['id'])) {
                    $mid = (int) $mat['id'];
                    $materialIds[] = $mid;
                    $materialIdToKey[$mid] = $mat['slug'] ?? $mk;
                }
            }
            $materialIds = array_values(array_unique($materialIds));
            if ($materialIds !== []) {
                $offerResult = $this->materialOfferService->getOfferListByMaterialIds([
                    'material_ids' => $materialIds,
                    'limit'        => 500,
                ]);
                $offerList = $offerResult['data']['list'] ?? [];
                $materialIdsSet = array_flip($materialIds);
                foreach ($offerList as $offer) {
                    $cid = (int) ($offer['company_id'] ?? 0);
                    if ($cid > 0) {
                        $companyIds[$cid] = true;
                    }
                }
                $companyIds = array_keys($companyIds);
            }
        }

        $supplierParams = [
            'status'     => 1,
            'list_type'  => 'active',
            'limit'      => 500,
        ];
        if ($companyIds !== []) {
            $supplierParams['id'] = $companyIds;
            $supplierParams['limit'] = count($companyIds);
        }
        $supplierList = $this->supplierService->getSupplierList($supplierParams);
        $suppliersById = [];
        foreach ($supplierList['data']['list'] ?? [] as $s) {
            $sid = (int) ($s['id'] ?? 0);
            if ($sid > 0) {
                $suppliersById[$sid] = $s;
            }
        }

        if ($industryKey !== '') {
            $allowedIds = [];
            foreach ($suppliersById as $sid => $s) {
                $subindustries = $s['industry_subindustries'] ?? [];
                if (!is_array($subindustries)) {
                    continue;
                }
                foreach ($subindustries as $pair) {
                    $ind = $pair['industry'] ?? [];
                    $backKey = $this->industryKeyFromSlug((string) ($ind['slug'] ?? ''));
                    if ($backKey === $industryKey) {
                        $allowedIds[$sid] = true;
                        break;
                    }
                }
            }
            $suppliersById = array_intersect_key($suppliersById, $allowedIds);
        }

        $materialIdsSet = array_flip($materialIds);
        $matches = [];
        foreach ($offerList as $offer) {
            $pid = (int) ($offer['parent_id'] ?? 0);
            $cid = (int) ($offer['company_id'] ?? 0);
            if ($pid <= 0 || $cid <= 0 || !isset($materialIdsSet[$pid]) || !isset($suppliersById[$cid])) {
                continue;
            }
            $supplier = $suppliersById[$cid];
            $materialKey = $materialIdToKey[$pid] ?? (string) $pid;
            $unitPriceRial = (int) ($offer['unit_price_rial'] ?? 0);
            $offerData = [
                'offer_id'          => (int) ($offer['id'] ?? 0),
                'material_id'       => $pid,
                'material_key'      => $materialKey,
                'unit_price_rial'   => $unitPriceRial,
                'unit_price'        => $unitPriceRial,
                'currency'          => trim((string) ($offer['currency'] ?? 'rial')),
                'unit'              => trim((string) ($offer['unit'] ?? 'kg')),
                'amount'            => trim((string) ($offer['amount'] ?? '')),
                'country_of_origin' => trim((string) ($offer['country_of_origin'] ?? '')),
                'offer_status'      => trim((string) ($offer['status'] ?? '')),
            ];
            $match = array_merge($supplier, $offerData);
            $matches[] = $match;
        }

        if ($materialKeyStr === '' || $materialIds === []) {
            $supplierListRaw = $this->supplierService->getSupplierList([
                'status'     => 1,
                'list_type'  => 'active',
                'limit'      => 500,
            ]);
            $matches = $supplierListRaw['data']['list'] ?? [];
            if ($industryKey !== '') {
                $filtered = [];
                foreach ($matches as $s) {
                    $subindustries = $s['industry_subindustries'] ?? [];
                    if (!is_array($subindustries)) {
                        continue;
                    }
                    foreach ($subindustries as $pair) {
                        $ind = $pair['industry'] ?? [];
                        $backKey = $this->industryKeyFromSlug((string) ($ind['slug'] ?? ''));
                        if ($backKey === $industryKey) {
                            $filtered[] = $s;
                            break;
                        }
                    }
                }
                $matches = $filtered;
            }
        }

        return [
            'result' => true,
            'data'   => ['matches' => $matches],
            'error'  => [],
        ];
    }
}
