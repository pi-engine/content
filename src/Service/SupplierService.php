<?php

namespace Content\Service;

use User\Service\AccountService as UserAccountService;
use User\Service\RoleService as UserRoleService;

use function is_array;
use function json_decode;
use function json_encode;
use function time;
use function uniqid;

class SupplierService implements ServiceInterface
{
    public const TYPE_SUPPLIER = 'supplier';

    /** @var ItemService */
    protected ItemService $itemService;

    /** @var UserAccountService */
    protected UserAccountService $accountService;

    /** @var UserRoleService */
    protected UserRoleService $roleService;

    /** @var SupplierReviewService|null */
    protected ?SupplierReviewService $reviewService = null;

    /** @var SupplierScoreService|null */
    protected ?SupplierScoreService $scoreService = null;

    public function __construct(
        ItemService $itemService,
        UserAccountService $accountService,
        UserRoleService $roleService,
        ?SupplierReviewService $reviewService = null,
        ?SupplierScoreService $scoreService = null
    ) {
        $this->itemService    = $itemService;
        $this->accountService = $accountService;
        $this->roleService    = $roleService;
        $this->reviewService  = $reviewService;
        $this->scoreService   = $scoreService;
    }

    /**
     * Add a supplier (content_item with type=supplier) and create an account for the supplier admin
     * with roles member and supplier.
     *
     * @param array $requestBody Supplier data (admin_*, company_*, industry_key, etc.)
     * @param array $account Current admin account
     * @return array
     */
    public function addSupplier(array $requestBody, array $account): array
    {
        $supplierAdminUserId = null;

        if (!empty($requestBody['admin_email']) || !empty($requestBody['admin_identity'])) {
            $this->roleService->ensureRoleExists('supplier', 'api', 'Supplier');

            if (!empty($requestBody['admin_identity'])) {
                $existingByIdentity = $this->accountService->getAccount(['identity' => $requestBody['admin_identity']]);
                if (!empty($existingByIdentity['id'])) {
                    return [
                        'result' => false,
                        'data'   => [],
                        'error'  => ['message' => 'نام کاربری تکراری بوده'],
                    ];
                }
            }

            $accountParams = [
                'first_name' => $requestBody['admin_first_name'] ?? '',
                'last_name'  => $requestBody['admin_last_name'] ?? '',
                'email'      => $requestBody['admin_email'] ?? null,
                'identity'   => $requestBody['admin_identity'] ?? null,
                'credential' => $requestBody['admin_credential'] ?? null,
            ];

            $newAccount = [];
            if (!empty($requestBody['admin_email'])) {
                $newAccount = $this->accountService->getAccount(['email' => $requestBody['admin_email']]);
            }
            if (empty($newAccount)) {
                $newAccount = $this->accountService->addAccount($accountParams, $account);
            }

            if (!empty($newAccount['id'])) {
                $supplierAdminUserId = (int) $newAccount['id'];
                $this->accountService->addRoleAccountByAdmin(
                    ['roles' => 'supplier'],
                    $newAccount,
                    $account
                );
            }
        }

        $slug = $requestBody['slug'] ?? 'supplier-' . ($account['id'] ?? 0) . '-' . uniqid();
        $title = $requestBody['company_name'] ?? $requestBody['company_title'] ?? 'Supplier ' . $slug;
        $timeCreate = time();

        $information = $this->sanitizeSupplierInformation($requestBody);
        if ($supplierAdminUserId !== null) {
            $information['admin_id'] = $supplierAdminUserId;
            $information['supplier_admin_user_id'] = $supplierAdminUserId;
        }

        $params = [
            'type' => self::TYPE_SUPPLIER,
            'slug' => $slug,
            'title' => $title,
            'status' => (int) ($requestBody['status'] ?? 1),
            'user_id' => (int) ($account['id'] ?? 0),
            'time_create' => $timeCreate,
            'time_update' => $timeCreate,
            'parent_id' => 0,
            'priority' => 0,
        ];

        $params['information'] = json_encode($information, JSON_UNESCAPED_UNICODE);
        $result = $this->itemService->addItem($params, $account);

        if ($supplierAdminUserId !== null && !empty($result['id'])) {
            $this->accountService->updateAccount(
                ['company_id' => (int) $result['id']],
                ['id' => $supplierAdminUserId],
                $account
            );
        }

        return $result;
    }

    /**
     * Public self-registration: add supplier with content_item status=1 (not deleted), information.registration_source=public.
     * User account is inactive (0) until admin approves. Tab "در انتظار تایید" = status=1 + registration_source=public + no was_activated_at.
     * content_item.status=0 is reserved for deleted suppliers only.
     * Duplicate admin_identity returns error "قبلا این یوزر نیم استفاده شده".
     *
     * @param array $requestBody Same fields as addSupplier (admin_*, company_*, industry_subindustries, etc.)
     * @return array Result or ['result' => false, 'error' => ['message' => '...'], 'data' => []]
     */
    public function addSupplierPublic(array $requestBody): array
    {
        if (empty($requestBody['admin_identity'])) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'نام کاربری الزامی است'],
            ];
        }

        $this->roleService->ensureRoleExists('supplier', 'api', 'Supplier');

        $existingByIdentity = $this->accountService->getAccount(['identity' => $requestBody['admin_identity']]);
        if (!empty($existingByIdentity['id'])) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'قبلا این یوزر نیم استفاده شده'],
            ];
        }

        $accountParams = [
            'first_name' => $requestBody['admin_first_name'] ?? '',
            'last_name'  => $requestBody['admin_last_name'] ?? '',
            'email'      => $requestBody['admin_email'] ?? null,
            'identity'   => $requestBody['admin_identity'],
            'credential' => $requestBody['admin_credential'] ?? null,
            'status'     => 0,
        ];
        $operator = [];
        $newAccount = $this->accountService->addAccount($accountParams, $operator);
        if (empty($newAccount['id'])) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'خطا در ایجاد حساب کاربری'],
            ];
        }

        $supplierAdminUserId = (int) $newAccount['id'];
        $this->roleService->addRoleAccount($newAccount, 'supplier', 'api');

        $slug   = $requestBody['slug'] ?? 'supplier-pub-' . uniqid();
        $title  = $requestBody['company_name'] ?? $requestBody['company_title'] ?? 'Supplier ' . $slug;
        $timeCreate = time();
        $information = $this->sanitizeSupplierInformation($requestBody);
        $information['admin_id'] = $supplierAdminUserId;
        $information['supplier_admin_user_id'] = $supplierAdminUserId;
        $information['registration_source'] = 'public';

        $params = [
            'type'        => self::TYPE_SUPPLIER,
            'slug'        => $slug,
            'title'       => $title,
            'status'      => 1,
            'user_id'     => $supplierAdminUserId,
            'time_create' => $timeCreate,
            'time_update' => $timeCreate,
            'parent_id'   => 0,
            'priority'    => 0,
            'information' => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];
        $fakeAccount = ['id' => $supplierAdminUserId];
        $result = $this->itemService->addItem($params, $fakeAccount);

        if (!empty($result['id'])) {
            $this->accountService->updateAccount(
                ['company_id' => (int) $result['id']],
                ['id' => $supplierAdminUserId],
                $operator
            );
        }

        return $result;
    }

    /**
     * List suppliers (content_item with type=supplier).
     * Supports order=rating_desc or sort_by=rating to sort by highest average rating.
     * Attaches average_rating and review_count when SupplierReviewService is available.
     *
     * @param array $params limit, page, order, sort_by, filters
     * @return array
     */
    public function getSupplierList(array $params): array
    {
        $limit  = (int) ($params['limit'] ?? 50);
        $page   = (int) ($params['page'] ?? 1);
        $offset = ($page - 1) * $limit;
        $sortByRating = !empty($params['order']) && (string) $params['order'] === 'rating_desc'
            || !empty($params['sort_by']) && (string) $params['sort_by'] === 'rating';

        if ($sortByRating && $this->scoreService !== null) {
            try {
                $ratingList = $this->scoreService->getSupplierIdsOrderedByOverallScore($limit, $offset);
                $supplierIds = $ratingList['supplier_ids'];
                $total = $ratingList['total'];
                if ($supplierIds !== []) {
                    $params['type']   = self::TYPE_SUPPLIER;
                    $params['status'] = $params['status'] ?? 1;
                    $params['id']     = $supplierIds;
                    $params['limit']  = count($supplierIds);
                    $params['offset'] = 0;
                    $result = $this->itemService->getItemList($params);
                    $list = $result['data']['list'] ?? [];
                    $byId = [];
                    foreach ($list as $item) {
                        $byId[(int) ($item['id'] ?? 0)] = $item;
                    }
                    $orderedList = [];
                    foreach ($supplierIds as $id) {
                        if (isset($byId[$id])) {
                            $orderedList[] = $this->stripSensitiveSupplierInfo($byId[$id]);
                        }
                    }
                    $result['data']['list'] = $orderedList;
                    $result['data']['paginator'] = ['count' => $total, 'limit' => $limit, 'page' => $page];
                    $this->attachRatingStats($result['data']['list']);
                    return $result;
                }
            } catch (\Throwable $e) {
                // Fall through to review-based or normal list
            }
        }

        if ($sortByRating && $this->reviewService !== null) {
            try {
                $ratingList = $this->reviewService->getSupplierIdsOrderedByRating($limit, $offset);
                $supplierIds = $ratingList['supplier_ids'];
                $total = $ratingList['total'];
                if ($supplierIds !== []) {
                    $params['type']   = self::TYPE_SUPPLIER;
                    $params['status'] = $params['status'] ?? 1;
                    $params['id']     = $supplierIds;
                    $params['limit']  = count($supplierIds);
                    $params['offset'] = 0;
                    $result = $this->itemService->getItemList($params);
                    $list = $result['data']['list'] ?? [];
                    $byId = [];
                    foreach ($list as $item) {
                        $byId[(int) ($item['id'] ?? 0)] = $item;
                    }
                    $orderedList = [];
                    foreach ($supplierIds as $id) {
                        if (isset($byId[$id])) {
                            $orderedList[] = $this->stripSensitiveSupplierInfo($byId[$id]);
                        }
                    }
                    $result['data']['list'] = $orderedList;
                    $result['data']['paginator'] = ['count' => $total, 'limit' => $limit, 'page' => $page];
                    $this->attachRatingStats($result['data']['list']);
                    return $result;
                }
            } catch (\Throwable $e) {
                // Table may not exist yet; fall back to normal list without rating sort
            }
        }

        $params['type'] = self::TYPE_SUPPLIER;
        // status=0 means deleted (soft delete). List only non-deleted (status=1).
        $params['status'] = 1;
        if (isset($params['list_type']) && in_array($params['list_type'], ['active', 'pending', 'inactive'], true)) {
            // keep list_type so ItemRepository filters by status_kind at DB level; pagination then works per tab
        } else {
            unset($params['list_type']);
        }
        // Keep id filter when caller passed it (e.g. workflow vendors by company_ids). Only rating-sort path sets id and returns early.
        // Do not pass order=rating_desc to ItemRepository (it is not a column)
        if (isset($params['order']) && (string) $params['order'] === 'rating_desc') {
            unset($params['order']);
        }
        if (isset($params['sort_by']) && (string) $params['sort_by'] === 'rating') {
            unset($params['sort_by']);
        }
        $result = $this->itemService->getItemList($params);
        if (!empty($result['data']['list']) && is_array($result['data']['list'])) {
            foreach ($result['data']['list'] as &$item) {
                $item = $this->stripSensitiveSupplierInfo($item);
            }
            unset($item);
            try {
                $this->attachRatingStats($result['data']['list']);
            } catch (\Throwable $e) {
                // Table content_supplier_review may not exist yet
            }
        }
        return $result;
    }

    /**
     * Attach average_rating and review_count to each supplier in the list.
     *
     * @param array $list
     */
    private function attachRatingStats(array &$list): void
    {
        if ($list === []) {
            return;
        }
        $ids = array_values(array_filter(array_unique(array_map(function ($item) {
            return isset($item['id']) ? (int) $item['id'] : null;
        }, $list))));
        if ($this->reviewService !== null) {
            $stats = $this->reviewService->getRatingStatsBySupplierIds($ids);
            foreach ($list as &$item) {
                $id = isset($item['id']) ? (int) $item['id'] : 0;
                $item['average_rating']    = $stats[$id]['average_rating'] ?? null;
                $item['review_count']     = $stats[$id]['review_count'] ?? 0;
                $item['total_rating_sum'] = $stats[$id]['total_rating_sum'] ?? 0;
            }
            unset($item);
        }
        if ($this->scoreService !== null) {
            try {
                $scoreStats = $this->scoreService->getAveragesForSupplierIds($ids);
                foreach ($list as &$item) {
                    $id = isset($item['id']) ? (int) $item['id'] : 0;
                    $item['score_averages'] = $scoreStats[$id]['score_averages'] ?? [];
                    $item['overall_score']  = $scoreStats[$id]['overall_score'] ?? null;
                }
                unset($item);
            } catch (\Throwable $e) {
                // score tables may not exist
            }
        }
    }

    /**
     * Get one supplier by id or slug.
     *
     * @param mixed $parameter id or slug
     * @param string $type 'id' or 'slug'
     * @return array
     */
    public function getSupplier($parameter, string $type = 'id'): array
    {
        $params = ['type' => self::TYPE_SUPPLIER];
        $item = $this->itemService->getItem((string) $parameter, $type, $params);
        if (empty($item)) {
            return [];
        }
        $item = $this->stripSensitiveSupplierInfo($item);
        if (!empty($item['id'])) {
            if ($this->reviewService !== null) {
                try {
                    $stats = $this->reviewService->getSupplierRatingStats((int) $item['id']);
                    $item['average_rating']    = $stats['average_rating'] ?? null;
                    $item['review_count']     = $stats['review_count'] ?? 0;
                    $item['total_rating_sum'] = $stats['total_rating_sum'] ?? 0;
                } catch (\Throwable $e) {
                    // Table content_supplier_review may not exist yet
                }
            }
            if ($this->scoreService !== null) {
                try {
                    $scoreData = $this->scoreService->getAveragesForSupplierIds([(int) $item['id']]);
                    $sid = (int) $item['id'];
                    $item['score_averages'] = $scoreData[$sid]['score_averages'] ?? [];
                    $item['overall_score']  = $scoreData[$sid]['overall_score'] ?? null;
                } catch (\Throwable $e) {
                    // score tables may not exist
                }
            }
        }
        return $item;
    }

    /**
     * Update a supplier.
     *
     * @param array $requestBody Must contain id or slug and fields to update
     * @param array $account Current admin account
     * @return array
     */
    public function editSupplier(array $requestBody, array $account): array
    {
        $id = $requestBody['id'] ?? null;
        $slug = $requestBody['slug'] ?? null;
        if (!$id && !$slug) {
            return [
                'result' => false,
                'data' => [],
                'error' => ['message' => 'id or slug is required'],
            ];
        }

        $existing = $id
            ? $this->itemService->getItem((string) $id, 'id', ['type' => self::TYPE_SUPPLIER])
            : $this->itemService->getItem((string) $slug, 'slug', ['type' => self::TYPE_SUPPLIER]);

        if (empty($existing)) {
            return [
                'result' => false,
                'data' => [],
                'error' => ['message' => 'Supplier not found'],
            ];
        }

        // canonizeItem returns decoded information merged with id/title/slug at top level (no 'information' key)
        // admin_id in content information = supplier admin user; user_id in content_item = creator of the record
        $supplierAdminUserId = null;
        if (!empty($existing['admin_id'])) {
            $supplierAdminUserId = (int) $existing['admin_id'];
        } elseif (!empty($existing['supplier_admin_user_id'])) {
            $supplierAdminUserId = (int) $existing['supplier_admin_user_id'];
        }

        $information = $existing;
        $nonInformationKeys = [
            'id', 'parent_id', 'title', 'slug', 'type', 'status', 'user_id',
            'time_create', 'time_update', 'time_delete', 'priority', 'time_create_view',
        ];
        foreach ($nonInformationKeys as $key) {
            unset($information[$key]);
        }
        $hasAdminFields = !empty($requestBody['admin_first_name']) || !empty($requestBody['admin_last_name'])
            || array_key_exists('admin_email', $requestBody) || !empty($requestBody['admin_identity'])
            || !empty($requestBody['admin_credential']);

        if ($supplierAdminUserId && $hasAdminFields) {
            $updateParams = [
                'user_id' => $supplierAdminUserId,
                'company_id' => (int) $existing['id'],
            ];
            if (array_key_exists('admin_first_name', $requestBody)) {
                $updateParams['first_name'] = $requestBody['admin_first_name'];
            }
            if (array_key_exists('admin_last_name', $requestBody)) {
                $updateParams['last_name'] = $requestBody['admin_last_name'];
            }
            if (array_key_exists('admin_email', $requestBody)) {
                $updateParams['email'] = $requestBody['admin_email'];
            }
            if (!empty($requestBody['admin_identity'])) {
                $updateParams['identity'] = $requestBody['admin_identity'];
            }
            if (!empty($requestBody['admin_credential'])) {
                $updateParams['credential'] = $requestBody['admin_credential'];
            }
            $updateResult = $this->accountService->updateAccountByAdmin($updateParams, $account);
            if (isset($updateResult['result']) && $updateResult['result'] === false) {
                return [
                    'result' => false,
                    'data' => $updateResult['data'] ?? [],
                    'error' => $updateResult['error'] ?? ['message' => 'Update failed'],
                ];
            }
        }

        $updatedInfo = $this->sanitizeSupplierInformation($requestBody);
        unset($updatedInfo['admin_credential']);
        $information = array_merge($information, $updatedInfo);

        if (isset($requestBody['status'])) {
            $reqStatus = (int) $requestBody['status'];

            if ($reqStatus === 1) {
                // Active
                $information['was_activated_at'] = $information['was_activated_at'] ?? time();
                $information['supplier_active'] = 1;
            } elseif ($reqStatus === 0) {
                // Inactive
                $information['supplier_active'] = 0;
            } elseif ($reqStatus === 2) {
                // Pending (under review) – only meaningful for public registrations
                $information['registration_source'] = $information['registration_source'] ?? 'public';
                unset($information['was_activated_at']);
                // Keep supplier_active as active (1) by default while blocking login via pending logic
                $information['supplier_active'] = $information['supplier_active'] ?? 1;
            }
        }

        $title = $requestBody['company_name'] ?? $requestBody['company_title'] ?? ($existing['title'] ?? '');
        $timeUpdate = time();

        $params = [
            'id' => $existing['id'],
            'title' => $title,
            'time_update' => $timeUpdate,
            'information' => json_encode($information, JSON_UNESCAPED_UNICODE),
        ];
        // content_item.status=0 is only for deleted; when admin changes status we keep status=1 at content_item level
        if (isset($requestBody['status']) && (int) $requestBody['status'] === 1) {
            $params['status'] = 1;
        }

        $this->itemService->editItem($params, $account);

        if ($supplierAdminUserId !== null && isset($requestBody['status'])) {
            $reqStatus = (int) $requestBody['status'];
            // User is allowed to log in only when supplier is "active"
            $userStatus = $reqStatus === 1 ? 1 : 0;
            $this->accountService->updateStatusByAdmin(
                ['user_id' => $supplierAdminUserId, 'status' => $userStatus],
                $account
            );
        }

        return $this->getSupplier((string) $existing['id'], 'id');
    }

    /**
     * Delete a supplier (soft delete via deleteItem).
     *
     * @param array $requestBody Must contain id or slug
     * @param array $account Current admin account
     * @return array
     */
    public function deleteSupplier(array $requestBody, array $account): array
    {
        $id = $requestBody['id'] ?? null;
        $slug = $requestBody['slug'] ?? null;
        if (!$id && !$slug) {
            return [
                'result' => false,
                'data' => [],
                'error' => ['message' => 'id or slug is required'],
            ];
        }

        $existing = $id
            ? $this->itemService->getItem((string) $id, 'id', ['type' => self::TYPE_SUPPLIER])
            : $this->itemService->getItem((string) $slug, 'slug', ['type' => self::TYPE_SUPPLIER]);

        if (empty($existing)) {
            return [
                'result' => false,
                'data' => [],
                'error' => ['message' => 'Supplier not found'],
            ];
        }

        $this->itemService->deleteItem(['id' => $existing['id']], $account);
        return ['result' => true, 'data' => [], 'error' => []];
    }

    /**
     * Normalize request body into supplier information (stored in content_item.information).
     * Accepts industry_subindustries as array of { industry: { id, slug, title }, sub_industry: { id, slug, title, parent_id } }
     * for multiple industry/sub-industry pairs per supplier.
     */
    private function sanitizeSupplierInformation(array $body): array
    {
        $allowed = [
            'admin_first_name',
            'admin_last_name',
            'admin_email',
            'admin_identity',
            'company_name',
            'company_title',
            'company_phone',
            'company_email',
            'company_website',
            'company_address',
            'country',
            'state',
            'city',
            'zip_code',
            'description',
            'industry_key',
            'sub_industry_key',
            'industry_id',
            'sub_industry_id',
            'economic_code',
            'national_id',
        ];
        $out = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $body)) {
                $out[$key] = $body[$key];
            }
        }
        if (isset($body['industry_subindustries']) && is_array($body['industry_subindustries'])) {
            $out['industry_subindustries'] = $this->normalizeIndustrySubindustries($body['industry_subindustries']);
        }
        return $out;
    }

    /**
     * Normalize industry_subindustries array for storage (full objects with id, slug, title).
     *
     * @param array $items Each item: industry: { id?, slug?, title? }, sub_industry: { id?, slug?, title?, parent_id? }
     * @return array
     */
    private function normalizeIndustrySubindustries(array $items): array
    {
        $normalized = [];
        foreach ($items as $item) {
            if (empty($item['industry']) || empty($item['sub_industry'])) {
                continue;
            }
            $ind = $item['industry'];
            $sub = $item['sub_industry'];
            $normalized[] = [
                'industry' => [
                    'id'    => $ind['id'] ?? null,
                    'slug'  => $ind['slug'] ?? null,
                    'title' => $ind['title'] ?? null,
                ],
                'sub_industry' => [
                    'id'         => $sub['id'] ?? null,
                    'slug'       => $sub['slug'] ?? null,
                    'title'      => $sub['title'] ?? null,
                    'parent_id'  => $sub['parent_id'] ?? null,
                ],
            ];
        }
        return $normalized;
    }

    /**
     * Remove sensitive fields from supplier item (for list/get API response).
     */
    private function stripSensitiveSupplierInfo(array $item): array
    {
        unset($item['admin_credential']);

        // Derive status details before overriding status field
        $statusDetails = $this->getSupplierStatusDetails($item);
        $item['status'] = $statusDetails['effective_status'];
        $item['status_kind'] = $statusDetails['kind']; // active | inactive | pending | deleted

        return $item;
    }

    /**
     * Compute effective status and kind for API:
     * - effective_status: 0 = deleted / pending / inactive, 1 = active.
     * - kind:
     *   - deleted  : content_item.status=0
     *   - pending  : registration_source=public AND no was_activated_at
     *   - inactive : information.supplier_active=0
     *   - active   : otherwise
     */
    private function getSupplierStatusDetails(array $item): array
    {
        $contentStatus = (int) ($item['status'] ?? 1);
        if ($contentStatus !== 1) {
            // Soft-deleted supplier
            return [
                'effective_status' => 0,
                'kind' => 'deleted',
            ];
        }

        // Pending: registered from public form and not yet approved by admin
        $isPending = isset($item['registration_source'])
            && $item['registration_source'] === 'public'
            && (empty($item['was_activated_at']));
        if ($isPending) {
            return [
                'effective_status' => 0,
                'kind' => 'pending',
            ];
        }

        // Explicit inactive flag set by admin
        $supplierActive = isset($item['supplier_active']) ? (int) $item['supplier_active'] : 1;
        if ($supplierActive === 0) {
            return [
                'effective_status' => 0,
                'kind' => 'inactive',
            ];
        }

        // Otherwise, treat as active
        return [
            'effective_status' => 1,
            'kind' => 'active',
        ];
    }
}
