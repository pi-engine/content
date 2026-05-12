<?php

namespace Content\Service;

use function is_array;
use function json_encode;
use function time;
use function trim;

/**
 * Industries and sub-industries (type=meta-industry).
 * Two levels only: industry (parent_id=0) and sub-industry (parent_id = id of industry).
 * Each sub-industry has exactly one parent industry.
 */
class IndustryService implements ServiceInterface
{
    public const TYPE_INDUSTRY = 'meta-industry';
    private const SLUG_PREFIX = 'meta-industry-';

    /** @var ItemService */
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * List industries (parent_id=0) or sub-industries (parent_id set).
     * Params: parent_id (0 = industries), title (search), limit, page.
     */
    public function getList(array $params): array
    {
        $params['type'] = self::TYPE_INDUSTRY;
        $params['status'] = $params['status'] ?? 1;
        if (isset($params['parent_id'])) {
            $params['parent_id'] = (int) $params['parent_id'];
        }
        return $this->itemService->getItemList($params);
    }

    /**
     * Get one industry or sub-industry by id or slug.
     */
    public function getIndustry(string $parameter, string $type = 'id'): array
    {
        $item = $this->itemService->getItem($parameter, $type, ['type' => self::TYPE_INDUSTRY]);
        return is_array($item) ? $item : [];
    }

    /**
     * Add industry (parent_id=0) or sub-industry (parent_id set).
     * Body: title, key (optional), parent_id (for sub-industry).
     */
    public function addIndustry(array $requestBody, array $account): array
    {
        $title = trim((string) ($requestBody['title'] ?? ''));
        $key = trim((string) ($requestBody['key'] ?? ''));
        $parentId = isset($requestBody['parent_id']) ? (int) $requestBody['parent_id'] : 0;

        if ($title === '') {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'Title is required.']];
        }

        // Only two levels: industry (parent_id=0) and sub-industry (parent_id = industry id).
        // Sub-industry can only have a top-level industry as parent, not another sub-industry.
        if ($parentId > 0) {
            $parent = $this->getIndustry((string) $parentId, 'id');
            if (empty($parent)) {
                return ['result' => false, 'data' => [], 'error' => ['message' => 'Parent industry not found.']];
            }
            $parentParentId = (int) ($parent['parent_id'] ?? 0);
            if ($parentParentId !== 0) {
                return ['result' => false, 'data' => [], 'error' => ['message' => 'Sub-industry can only have an industry as parent (two levels only).']];
            }
            $parentKey = $this->slugToKey($parent['slug'] ?? '', true);
            $subKey = $key !== '' ? $key : $this->titleToKey($title);
            $slug = self::SLUG_PREFIX . $parentKey . '-' . $subKey;
        } else {
            $industryKey = $key !== '' ? $key : $this->titleToKey($title);
            $slug = self::SLUG_PREFIX . $industryKey;
        }

        $timeCreate = time();
        $params = [
            'type' => self::TYPE_INDUSTRY,
            'slug' => $slug,
            'title' => $title,
            'status' => (int) ($requestBody['status'] ?? 1),
            'user_id' => (int) ($account['id'] ?? 0),
            'time_create' => $timeCreate,
            'time_update' => $timeCreate,
            'time_delete' => 0,
            'parent_id' => $parentId,
            'information' => '{}',
            'priority' => (int) ($requestBody['priority'] ?? 0),
        ];
        $result = $this->itemService->addItem($params, $account);
        return is_array($result) ? ['result' => true, 'data' => $result, 'error' => []] : ['result' => true, 'data' => $result ? (array) $result : [], 'error' => []];
    }

    /**
     * Edit industry or sub-industry.
     */
    public function editIndustry(array $requestBody, array $account): array
    {
        $id = $requestBody['id'] ?? $requestBody['slug'] ?? null;
        if ($id === null || $id === '') {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'id or slug required']];
        }
        $by = isset($requestBody['slug']) && $requestBody['slug'] !== '' ? 'slug' : 'id';
        $existing = $this->getIndustry((string) $id, $by);
        if (empty($existing)) {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'Industry not found']];
        }

        $title = trim((string) ($requestBody['title'] ?? $existing['title'] ?? ''));
        $key = trim((string) ($requestBody['key'] ?? ''));
        $parentId = (int) ($existing['parent_id'] ?? 0);

        $slug = $existing['slug'] ?? '';
        if ($key !== '') {
            if ($parentId > 0) {
                $parent = $this->getIndustry((string) $parentId, 'id');
                $parentKey = $this->slugToKey($parent['slug'] ?? '', true);
                $slug = self::SLUG_PREFIX . $parentKey . '-' . $key;
            } else {
                $slug = self::SLUG_PREFIX . $key;
            }
        }

        $params = [
            'id' => $existing['id'],
            'title' => $title,
            'slug' => $slug,
            'time_update' => time(),
        ];
        if (isset($requestBody['status'])) {
            $params['status'] = (int) $requestBody['status'];
        }
        $this->itemService->editItem($params, $account);
        return ['result' => true, 'data' => $this->getIndustry((string) $existing['id'], 'id'), 'error' => []];
    }

    /**
     * Soft-delete industry or sub-industry.
     */
    public function deleteIndustry(array $requestBody, array $account): array
    {
        $id = $requestBody['id'] ?? null;
        if ($id === null || $id === '') {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'id required']];
        }
        $this->itemService->deleteItem(['id' => $id], $account);
        return ['result' => true, 'data' => [], 'error' => []];
    }

    private function slugToKey(string $slug, bool $isParent = false): string
    {
        if ($slug === '') {
            return '';
        }
        $prefix = self::SLUG_PREFIX;
        if (strpos($slug, $prefix) !== 0) {
            return $slug;
        }
        $rest = substr($slug, strlen($prefix));
        if ($isParent) {
            return $rest;
        }
        $parts = explode('-', $rest);
        return $parts[0] ?? $rest;
    }

    private function titleToKey(string $title): string
    {
        $key = preg_replace('/[^a-z0-9]+/i', '_', $title);
        $key = trim($key, '_');
        return $key !== '' ? $key : 'item_' . time();
    }
}
