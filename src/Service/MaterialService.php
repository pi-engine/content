<?php

namespace Content\Service;

use function is_array;
use function json_encode;
use function time;
use function trim;

class MaterialService implements ServiceInterface
{
    public const TYPE_MATERIAL = 'material';

    /** @var ItemService */
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Add a material (content_item with type=material).
     * Request: name, title, key, value, sub_industries_keys (array)
     */
    public function addMaterial(array $requestBody, array $account): array
    {
        $key = trim((string) ($requestBody['key'] ?? ''));
        $slug = $key !== '' ? $key : 'material-' . time();
        $title = trim((string) ($requestBody['title'] ?? $requestBody['name'] ?? $slug));
        $timeCreate = time();

        $information = $this->sanitizeMaterialInformation($requestBody);

        $params = [
            'type' => self::TYPE_MATERIAL,
            'slug' => $slug,
            'title' => $title,
            'status' => (int) ($requestBody['status'] ?? 1),
            'user_id' => (int) ($account['id'] ?? 0),
            'time_create' => $timeCreate,
            'time_update' => $timeCreate,
            'parent_id' => 0,
            'priority' => (int) ($requestBody['priority'] ?? 0),
        ];
        $params['information'] = json_encode($information, JSON_UNESCAPED_UNICODE);
        return $this->itemService->addItem($params, $account);
    }

    /**
     * List materials (content_item with type=material).
     */
    public function getMaterialList(array $params): array
    {
        $params['type'] = self::TYPE_MATERIAL;
        $params['status'] = $params['status'] ?? 1;
        return $this->itemService->getItemList($params);
    }

    /**
     * Get one material by id or slug.
     */
    public function getMaterial($parameter, string $type = 'id'): array
    {
        $params = ['type' => self::TYPE_MATERIAL];
        $item = $this->itemService->getItem((string) $parameter, $type, $params);
        return is_array($item) ? $item : [];
    }

    /**
     * Update a material.
     */
    public function editMaterial(array $requestBody, array $account): array
    {
        $id = $requestBody['id'] ?? null;
        $slug = $requestBody['slug'] ?? null;
        if (!$id && !$slug) {
            return [
                'result' => false,
                'data' => [],
                'error' => ['message' => 'id or slug required'],
            ];
        }

        $existing = $id
            ? $this->itemService->getItem((string) $id, 'id', ['type' => self::TYPE_MATERIAL])
            : $this->itemService->getItem((string) $slug, 'slug', ['type' => self::TYPE_MATERIAL]);

        if (empty($existing)) {
            return [
                'result' => false,
                'data' => [],
                'error' => ['message' => 'Material not found'],
            ];
        }

        $information = $this->sanitizeMaterialInformation($requestBody);
        $title = trim((string) ($requestBody['title'] ?? $requestBody['name'] ?? $existing['title'] ?? ''));
        $key = trim((string) ($requestBody['key'] ?? ''));
        if ($key !== '' && isset($existing['slug'])) {
            $information['key'] = $key;
        }

        $params = [
            'id' => $existing['id'],
            'title' => $title,
            'time_update' => time(),
            'information' => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];
        if (isset($requestBody['status'])) {
            $params['status'] = (int) $requestBody['status'];
        }
        if ($key !== '') {
            $params['slug'] = $key;
        }

        $this->itemService->editItem($params, $account);
        return $this->getMaterial((string) $existing['id'], 'id');
    }

    private function sanitizeMaterialInformation(array $body): array
    {
        $out = [
            'name' => trim((string) ($body['name'] ?? '')),
            'title' => trim((string) ($body['title'] ?? '')),
            'key' => trim((string) ($body['key'] ?? '')),
            'value' => trim((string) ($body['value'] ?? '')),
        ];
        if (isset($body['sub_industries_keys']) && is_array($body['sub_industries_keys'])) {
            $out['sub_industries_keys'] = array_values(array_filter(array_map('trim', $body['sub_industries_keys'])));
        } else {
            $out['sub_industries_keys'] = [];
        }
        return $out;
    }
}
