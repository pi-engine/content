<?php

namespace Content\Service;

use function is_array;
use function json_decode;
use function json_encode;
use function time;
use function uniqid;

/**
 * Service for supplier material offers (رکورد قیمت/میزان ساخت برای هر ماده).
 * Stored as content_item with type=material_offer, parent_id=material_id.
 * Company = تأمین‌کننده (از company_id حل می‌شود). کشور سازنده = country_of_origin از کاربر.
 * information: { company_id, company_name, country_of_origin, amount, unit_price_rial, status, currency }
 */
class MaterialOfferService implements ServiceInterface
{
    public const TYPE_OFFER = 'material_offer';
    public const STATUS_PENDING = 'در انتظار تایید';
    public const STATUS_APPROVED = 'تایید شده';
    /** وضعیت پس از ویرایش توسط مدیر تأمین‌کننده */
    public const STATUS_UNDER_REVIEW = 'در دست بررسی';
    public const CURRENCY_RIAL = 'rial';

    /** @var ItemService */
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Add a material offer (one record: تأمین‌کننده = شرکت از company_id، کشور سازنده، میزان ساخت، مبلغ واحد).
     * شرکت = همان نام تأمین‌کننده است و از company_id حل می‌شود. کشور سازنده از کاربر گرفته می‌شود.
     * مبلغ واحد همیشه ریال ذخیره می‌شود (در فرم نمایش داده نمی‌شود).
     *
     * @param array $requestBody material_slug, company_id, country_of_origin, amount, unit_price_rial
     * @param array $account Current user (supplier admin)
     * @return array
     */
    public function addOffer(array $requestBody, array $account): array
    {
        $materialSlug = $requestBody['material_slug'] ?? null;
        if (empty($materialSlug)) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'material_slug is required'],
            ];
        }
        $material = $this->itemService->getItem((string) $materialSlug, 'slug', ['type' => 'material']);
        if (empty($material['id'])) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Material not found'],
            ];
        }
        $materialId = (int) $material['id'];

        $companyId = (int) ($requestBody['company_id'] ?? 0);
        $supplier  = [];
        if ($companyId > 0) {
            $supplier = $this->itemService->getItem((string) $companyId, 'id', ['type' => 'supplier']);
        }
        $companyName    = !empty($supplier['title']) ? trim((string) $supplier['title']) : (trim((string) ($supplier['company_name'] ?? '')));
        $countryOrigin  = trim((string) ($requestBody['country_of_origin'] ?? ''));
        $amount         = trim((string) ($requestBody['amount'] ?? ''));
        $unitPrice      = (int) ($requestBody['unit_price_rial'] ?? 0);

        if ($unitPrice < 0) {
            $unitPrice = 0;
        }

        $slug  = 'offer-' . $materialSlug . '-' . uniqid();
        $title = $companyName !== '' ? $companyName . ' - ' . $amount : $amount;
        $time  = time();

        $information = [
            'company_id'        => $companyId,
            'company_name'      => $companyName,
            'country_of_origin' => $countryOrigin,
            'amount'            => $amount,
            'unit_price_rial'   => $unitPrice,
            'status'            => self::STATUS_PENDING,
            'currency'          => self::CURRENCY_RIAL,
        ];

        $params = [
            'type'         => self::TYPE_OFFER,
            'slug'         => $slug,
            'title'        => $title,
            'status'       => 1,
            'user_id'      => (int) ($account['id'] ?? 0),
            'time_create'  => $time,
            'time_update'  => $time,
            'time_delete'  => 0,
            'parent_id'    => $materialId,
            'priority'     => 0,
            'information'  => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];

        $item = $this->itemService->addItem($params, $account);
        return [
            'result' => true,
            'data'   => is_array($item) ? $item : [],
            'error'  => [],
        ];
    }

    /**
     * List offers for a material (by material slug or id).
     *
     * @param array $params material_slug or parent_id, limit, page
     * @return array
     */
    public function getOfferList(array $params): array
    {
        $materialSlug = $params['material_slug'] ?? null;
        $parentId     = isset($params['parent_id']) ? (int) $params['parent_id'] : null;

        if ($parentId <= 0 && !empty($materialSlug)) {
            $material = $this->itemService->getItem((string) $materialSlug, 'slug', ['type' => 'material']);
            $parentId = !empty($material['id']) ? (int) $material['id'] : 0;
        }

        if ($parentId <= 0) {
            return [
                'result' => true,
                'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => 25, 'page' => 1]],
                'error'  => [],
            ];
        }

        $listParams = [
            'type'      => self::TYPE_OFFER,
            'parent_id' => $parentId,
            'status'    => $params['status'] ?? 1,
            'limit'     => (int) ($params['limit'] ?? 100),
            'page'      => (int) ($params['page'] ?? 1),
            'order'     => $params['order'] ?? ['time_create DESC', 'id DESC'],
        ];
        $companyId = isset($params['company_id']) ? (int) $params['company_id'] : 0;
        if ($companyId > 0) {
            $listParams['company_id'] = $companyId;
        }

        $result = $this->itemService->getItemList($listParams);
        if (!isset($result['data']['list'])) {
            return [
                'result' => true,
                'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => $listParams['limit'], 'page' => $listParams['page']]],
                'error'  => [],
            ];
        }

        return [
            'result' => true,
            'data'   => $result['data'],
            'error'  => [],
        ];
    }

    /**
     * List all offers (admin). Optional filters: company_id, offer_status, sub_industries_keys, material_title, country_of_origin.
     * Sort by price: sort_price = 'desc' (گران به ارزان) or 'asc'.
     * Each item includes material_title and company_name.
     *
     * @param array $params company_id?, offer_status?, sub_industries_keys?, material_title?, country_of_origin?, sort_price?, limit, page
     * @return array
     */
    public function getOfferListAll(array $params): array
    {
        $limit             = (int) ($params['limit'] ?? 100);
        $page              = (int) ($params['page'] ?? 1);
        $offset            = ($page - 1) * $limit;
        $companyId         = isset($params['company_id']) ? (int) $params['company_id'] : 0;
        $offerStatus       = isset($params['offer_status']) ? trim((string) $params['offer_status']) : '';
        $materialTitle     = isset($params['material_title']) ? trim((string) $params['material_title']) : '';
        $subIndustriesKeys = isset($params['sub_industries_keys']) && is_array($params['sub_industries_keys'])
            ? array_values(array_filter(array_map('trim', $params['sub_industries_keys']))) : [];
        $countryOfOrigin   = isset($params['country_of_origin']) ? trim((string) $params['country_of_origin']) : '';
        $sortPrice         = isset($params['sort_price']) ? strtolower((string) $params['sort_price']) : '';

        $parentIds = null;
        if ($materialTitle !== '' || $subIndustriesKeys !== []) {
            $materialParams = [
                'type'   => 'material',
                'status' => 1,
                'limit'  => 500,
                'page'   => 1,
                'offset' => 0,
            ];
            if ($materialTitle !== '') {
                $materialParams['title'] = $materialTitle;
            }
            if ($subIndustriesKeys !== []) {
                $materialParams['sub_industries_keys'] = $subIndustriesKeys;
            }
            $materialResult = $this->itemService->getItemList($materialParams);
            $materials      = $materialResult['data']['list'] ?? [];
            $parentIds      = array_values(array_filter(array_map(function ($m) {
                return isset($m['id']) ? (int) $m['id'] : null;
            }, $materials)));
            if ($parentIds === []) {
                return [
                    'result' => true,
                    'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => $limit, 'page' => $page]],
                    'error'  => [],
                ];
            }
        }

        $listParams = [
            'type'       => self::TYPE_OFFER,
            'status'     => 1,
            'limit'      => $limit,
            'page'       => $page,
            'offset'     => $offset,
            'order'      => ['time_create DESC', 'id DESC'],
        ];
        if ($companyId > 0) {
            $listParams['company_id'] = $companyId;
        }
        if ($offerStatus !== '') {
            $listParams['offer_status'] = $offerStatus;
        }
        if ($countryOfOrigin !== '') {
            $listParams['country_of_origin'] = $countryOfOrigin;
        }
        if ($sortPrice === 'asc' || $sortPrice === 'desc') {
            $listParams['order_by_price_rial'] = $sortPrice;
        }
        if ($parentIds !== null) {
            $listParams['parent_id'] = $parentIds;
        }

        $result = $this->itemService->getItemList($listParams);
        $list   = $result['data']['list'] ?? [];
        $count  = $result['data']['paginator']['count'] ?? 0;

        $materialCache = [];
        foreach ($list as &$item) {
            $pid = isset($item['parent_id']) ? (int) $item['parent_id'] : 0;
            if ($pid > 0) {
                if (!isset($materialCache[$pid])) {
                    $mat = $this->itemService->getItem((string) $pid, 'id', ['type' => 'material']);
                    $materialCache[$pid] = !empty($mat['title']) ? $mat['title'] : ($mat['name'] ?? '');
                }
                $item['material_title'] = $materialCache[$pid];
            } else {
                $item['material_title'] = '';
            }
        }
        unset($item);

        return [
            'result' => true,
            'data'   => [
                'list'     => $list,
                'paginator' => ['count' => $count, 'limit' => $limit, 'page' => $page],
            ],
            'error'  => [],
        ];
    }

    /**
     * List offers by supplier (company_id). Optional filters: material_title, sub_industries_keys.
     * Each item includes material_title from parent material.
     *
     * @param array $params company_id, material_title, sub_industries_keys, limit, page
     * @return array
     */
    public function getOfferListBySupplier(array $params): array
    {
        $companyId = isset($params['company_id']) ? (int) $params['company_id'] : 0;
        if ($companyId <= 0) {
            return [
                'result' => true,
                'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => 50, 'page' => 1]],
                'error'  => [],
            ];
        }

        $materialTitle     = isset($params['material_title']) ? trim((string) $params['material_title']) : '';
        $subIndustriesKeys = isset($params['sub_industries_keys']) && is_array($params['sub_industries_keys'])
            ? array_values(array_filter(array_map('trim', $params['sub_industries_keys']))) : [];
        $limit             = (int) ($params['limit'] ?? 50);
        $page              = (int) ($params['page'] ?? 1);
        $offset            = ($page - 1) * $limit;
        $order             = $params['order'] ?? ['time_create DESC', 'id DESC'];

        $parentIds = null;
        if ($materialTitle !== '' || $subIndustriesKeys !== []) {
            $materialParams = [
                'type'   => 'material',
                'status' => 1,
                'limit'  => 500,
                'page'   => 1,
                'offset' => 0,
            ];
            if ($materialTitle !== '') {
                $materialParams['title'] = $materialTitle;
            }
            if ($subIndustriesKeys !== []) {
                $materialParams['sub_industries_keys'] = $subIndustriesKeys;
            }
            $materialResult = $this->itemService->getItemList($materialParams);
            $materials      = $materialResult['data']['list'] ?? [];
            $parentIds      = array_values(array_filter(array_map(function ($m) {
                return isset($m['id']) ? (int) $m['id'] : null;
            }, $materials)));
            if ($parentIds === []) {
                return [
                    'result' => true,
                    'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => $limit, 'page' => $page]],
                    'error'  => [],
                ];
            }
        }

        $listParams = [
            'type'      => self::TYPE_OFFER,
            'company_id' => $companyId,
            'status'    => $params['status'] ?? 1,
            'limit'     => $limit,
            'page'      => $page,
            'offset'    => $offset,
            'order'     => $order,
        ];
        if ($parentIds !== null) {
            $listParams['parent_id'] = $parentIds;
        }

        $result = $this->itemService->getItemList($listParams);
        $list   = $result['data']['list'] ?? [];
        $count  = $result['data']['paginator']['count'] ?? 0;

        $materialCache = [];
        foreach ($list as &$item) {
            $pid = isset($item['parent_id']) ? (int) $item['parent_id'] : 0;
            if ($pid > 0) {
                if (!isset($materialCache[$pid])) {
                    $mat = $this->itemService->getItem((string) $pid, 'id', ['type' => 'material']);
                    $materialCache[$pid] = !empty($mat['title']) ? $mat['title'] : ($mat['name'] ?? '');
                }
                $item['material_title'] = $materialCache[$pid];
            } else {
                $item['material_title'] = '';
            }
        }
        unset($item);

        return [
            'result' => true,
            'data'   => [
                'list'     => $list,
                'paginator' => ['count' => $count, 'limit' => $limit, 'page' => $page],
            ],
            'error'  => [],
        ];
    }

    /**
     * List approved offers for given material ids (parent_id). Used by workflow/vendors public endpoint.
     *
     * @param array $params material_ids (array of material id), limit
     * @return array
     */
    public function getOfferListByMaterialIds(array $params): array
    {
        $materialIds = isset($params['material_ids']) && is_array($params['material_ids'])
            ? array_values(array_filter(array_map('intval', $params['material_ids']))) : [];
        if ($materialIds === []) {
            return [
                'result' => true,
                'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => 500, 'page' => 1]],
                'error'  => [],
            ];
        }
        $limit = (int) ($params['limit'] ?? 500);
        $listParams = [
            'type'       => self::TYPE_OFFER,
            'parent_id'  => $materialIds,
            'status'     => 1,
            'offer_status' => self::STATUS_APPROVED,
            'limit'      => $limit,
            'page'       => 1,
        ];
        $result = $this->itemService->getItemList($listParams);
        return [
            'result' => true,
            'data'   => $result['data'] ?? ['list' => [], 'paginator' => ['count' => 0, 'limit' => $limit, 'page' => 1]],
            'error'  => [],
        ];
    }

    /**
     * Update a material offer (country_of_origin, amount, unit_price_rial, status).
     *
     * @param array $requestBody id, country_of_origin, amount, unit_price_rial
     * @param array $account Current user
     * @return array
     */
    public function editOffer(array $requestBody, array $account): array
    {
        $id = isset($requestBody['id']) ? (int) $requestBody['id'] : 0;
        if ($id <= 0) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'id is required'],
            ];
        }

        $existing = $this->itemService->getItem((string) $id, 'id', ['type' => self::TYPE_OFFER]);
        if (empty($existing['id'])) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Offer not found'],
            ];
        }

        $information = [
            'company_id'        => (int) ($existing['company_id'] ?? 0),
            'company_name'      => trim((string) ($existing['company_name'] ?? '')),
            'country_of_origin' => trim((string) ($existing['country_of_origin'] ?? '')),
            'amount'            => trim((string) ($existing['amount'] ?? '')),
            'unit_price_rial'   => (int) ($existing['unit_price_rial'] ?? 0),
            'status'            => trim((string) ($existing['status'] ?? self::STATUS_PENDING)),
            'currency'          => trim((string) ($existing['currency'] ?? self::CURRENCY_RIAL)),
        ];
        if (array_key_exists('country_of_origin', $requestBody)) {
            $information['country_of_origin'] = trim((string) ($requestBody['country_of_origin'] ?? ''));
        }
        if (array_key_exists('amount', $requestBody)) {
            $information['amount'] = trim((string) ($requestBody['amount'] ?? ''));
        }
        if (array_key_exists('unit_price_rial', $requestBody)) {
            $unitPrice = (int) ($requestBody['unit_price_rial'] ?? 0);
            $information['unit_price_rial'] = $unitPrice >= 0 ? $unitPrice : $information['unit_price_rial'];
        }
        if (array_key_exists('status', $requestBody) && trim((string) $requestBody['status']) !== '') {
            $information['status'] = trim((string) $requestBody['status']);
        } else {
            // وقتی از پنل تأمین‌کننده ویرایش می‌شود (بدون ارسال status)، وضعیت به «در دست بررسی» تغییر کند
            $information['status'] = self::STATUS_UNDER_REVIEW;
        }

        $companyName = $information['company_name'];
        $amount      = $information['amount'];
        $title       = $companyName !== '' ? $companyName . ' - ' . $amount : $amount;

        $params = [
            'id'          => $id,
            'title'       => $title,
            'time_update' => time(),
            'information' => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];

        $this->itemService->editItem($params, $account);
        $updated = $this->itemService->getItem((string) $id, 'id', ['type' => self::TYPE_OFFER]);
        return [
            'result' => true,
            'data'   => is_array($updated) ? $updated : [],
            'error'  => [],
        ];
    }

    /**
     * Delete a material offer (soft delete).
     *
     * @param array $requestBody id
     * @param array $account Current user
     * @return array
     */
    public function deleteOffer(array $requestBody, array $account): array
    {
        $id = isset($requestBody['id']) ? (int) $requestBody['id'] : 0;
        if ($id <= 0) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'id is required'],
            ];
        }

        $existing = $this->itemService->getItem((string) $id, 'id', ['type' => self::TYPE_OFFER]);
        if (empty($existing['id'])) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Offer not found'],
            ];
        }

        $this->itemService->deleteItem(['id' => $id], $account);
        return [
            'result' => true,
            'data'   => [],
            'error'  => [],
        ];
    }

    /**
     * آمار قیمت‌گذاری برای یک ماده: حداقل، حداکثر، میانگین و تعداد پیشنهادهای تأییدشده (سایر تأمین‌کنندگان).
     * برای استفاده در Agent قیمت‌گذاری پنل تأمین‌کننده.
     *
     * @param string $materialSlug       slug ماده
     * @param int    $excludeCompanyId   company_id تأمین‌کننده فعلی تا رکوردهای خودش در آمار نباشد (۰ = همه)
     * @return array{result: bool, data: array{count: int, min_price_rial: int, max_price_rial: int, avg_price_rial: float, by_country: list, material_title: string}, error: array}
     */
    public function getPricingStatsForMaterial(string $materialSlug, int $excludeCompanyId = 0): array
    {
        $material = $this->itemService->getItem($materialSlug, 'slug', ['type' => 'material']);
        if (empty($material['id'])) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Material not found'],
            ];
        }
        $materialId    = (int) $material['id'];
        $materialTitle = trim((string) ($material['title'] ?? $material['name'] ?? $materialSlug));

        $listParams = [
            'type'         => self::TYPE_OFFER,
            'parent_id'     => [$materialId],
            'status'        => 1,
            'offer_status'  => self::STATUS_APPROVED,
            'limit'         => 500,
            'page'          => 1,
        ];
        $result = $this->itemService->getItemList($listParams);
        $list   = $result['data']['list'] ?? [];

        if ($excludeCompanyId > 0) {
            $list = array_values(array_filter($list, static function ($row) use ($excludeCompanyId) {
                $cid = (int) ($row['company_id'] ?? 0);
                return $cid !== $excludeCompanyId;
            }));
        }

        $prices   = [];
        $byCountry = [];
        foreach ($list as $row) {
            $price = (int) ($row['unit_price_rial'] ?? 0);
            if ($price >= 0) {
                $prices[] = $price;
                $country = trim((string) ($row['country_of_origin'] ?? ''));
                if ($country !== '') {
                    if (!isset($byCountry[$country])) {
                        $byCountry[$country] = ['count' => 0, 'sum' => 0];
                    }
                    $byCountry[$country]['count']++;
                    $byCountry[$country]['sum'] += $price;
                }
            }
        }

        $count = count($prices);
        $minPrice = $count > 0 ? min($prices) : 0;
        $maxPrice = $count > 0 ? max($prices) : 0;
        $avgPrice = $count > 0 ? (int) round(array_sum($prices) / $count) : 0;

        $byCountryList = [];
        foreach ($byCountry as $country => $data) {
            $byCountryList[] = [
                'country'         => $country,
                'count'           => $data['count'],
                'avg_price_rial'  => (int) round($data['sum'] / $data['count']),
            ];
        }

        return [
            'result' => true,
            'data'   => [
                'count'            => $count,
                'min_price_rial'   => $minPrice,
                'max_price_rial'   => $maxPrice,
                'avg_price_rial'   => $avgPrice,
                'by_country'       => $byCountryList,
                'material_title'   => $materialTitle,
            ],
            'error'  => [],
        ];
    }
}
