<?php

declare(strict_types=1);

namespace Content\Service;

use Content\Repository\SupplierReviewRepository;
use User\Service\AccountService as UserAccountService;
use function is_array;
use function trim;

/**
 * Supplier reviews: one rating per user/supplier (updatable), multiple comments per user/supplier.
 */
class SupplierReviewService implements ServiceInterface
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const RATING_MIN = 1;
    public const RATING_MAX = 5;

    public const TYPE_RATING  = 'rating';
    public const TYPE_COMMENT = 'comment';

    public function __construct(
        private SupplierReviewRepository $reviewRepository,
        private ItemService $itemService,
        private ?UserAccountService $accountService = null
    ) {
    }

    private function enrichListWithUserInfo(array &$list): void
    {
        if ($this->accountService === null || $list === []) {
            return;
        }
        $userIds = array_unique(array_filter(array_column($list, 'user_id')));
        $userInfo = [];
        foreach ($userIds as $uid) {
            try {
                $profile = $this->accountService->getProfile(['user_id' => (int) $uid]);
                $firstName = trim((string) ($profile['first_name'] ?? ''));
                $lastName  = trim((string) ($profile['last_name'] ?? ''));
                $userInfo[(int) $uid] = [
                    'user_name'  => trim($firstName . ' ' . $lastName) ?: ($profile['identity'] ?? (string) $uid),
                    'user_email' => $profile['email'] ?? '',
                ];
            } catch (\Throwable $e) {
                $userInfo[(int) $uid] = ['user_name' => (string) $uid, 'user_email' => ''];
            }
        }
        foreach ($list as &$row) {
            $uid = (int) ($row['user_id'] ?? 0);
            $row['user_name']  = $userInfo[$uid]['user_name'] ?? (string) $uid;
            $row['user_email'] = $userInfo[$uid]['user_email'] ?? '';
            $row['review_type_label'] = ($row['review_type'] ?? '') === self::TYPE_COMMENT ? 'نظر' : 'امتیاز';
        }
        unset($row);
    }

    /**
     * User submits a review for a supplier. Status = pending until admin approves.
     */
    public function addReview(array $requestBody, array $account): array
    {
        $userId = (int) ($account['id'] ?? 0);
        if ($userId <= 0) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Unauthorized'],
            ];
        }

        $supplierId = isset($requestBody['supplier_id']) ? (int) $requestBody['supplier_id'] : 0;
        $supplierSlug = isset($requestBody['supplier_slug']) ? trim((string) $requestBody['supplier_slug']) : '';
        if ($supplierId <= 0 && $supplierSlug === '') {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'supplier_id or supplier_slug is required'],
            ];
        }

        if ($supplierId <= 0) {
            $supplier = $this->itemService->getItem($supplierSlug, 'slug', ['type' => SupplierService::TYPE_SUPPLIER]);
            if (empty($supplier['id'])) {
                return [
                    'result' => false,
                    'data'   => [],
                    'error'  => ['message' => 'Supplier not found'],
                ];
            }
            $supplierId = (int) $supplier['id'];
        } else {
            $supplier = $this->itemService->getItem((string) $supplierId, 'id', ['type' => SupplierService::TYPE_SUPPLIER]);
            if (empty($supplier['id'])) {
                return [
                    'result' => false,
                    'data'   => [],
                    'error'  => ['message' => 'Supplier not found'],
                ];
            }
        }

        $rating = isset($requestBody['rating']) ? (int) $requestBody['rating'] : 0;
        if ($rating < self::RATING_MIN || $rating > self::RATING_MAX) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'rating must be between ' . self::RATING_MIN . ' and ' . self::RATING_MAX],
            ];
        }

        $comment = isset($requestBody['comment']) ? trim((string) $requestBody['comment']) : null;
        if ($comment === '') {
            $comment = null;
        }

        try {
            $row = $this->reviewRepository->add($supplierId, $userId, $rating, $comment);
            return [
                'result' => true,
                'data'   => $row,
                'error'  => [],
            ];
        } catch (\Throwable $e) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'امکان ثبت نظر در حال حاضر وجود ندارد. لطفاً بعداً تلاش کنید.'],
            ];
        }
    }

    /**
     * Set or update the single rating for (user, supplier). One rating per user per supplier.
     */
    public function addOrUpdateRating(array $requestBody, array $account): array
    {
        $userId = (int) ($account['id'] ?? 0);
        if ($userId <= 0) {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'Unauthorized']];
        }
        $supplierId = $this->resolveSupplierId($requestBody);
        if ($supplierId <= 0) {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'supplier_id or supplier_slug is required']];
        }
        $rating = isset($requestBody['rating']) ? (int) $requestBody['rating'] : 0;
        if ($rating < self::RATING_MIN || $rating > self::RATING_MAX) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'rating must be between ' . self::RATING_MIN . ' and ' . self::RATING_MAX],
            ];
        }
        try {
            $row = $this->reviewRepository->addOrUpdateRating($supplierId, $userId, $rating);
            return ['result' => true, 'data' => $row, 'error' => []];
        } catch (\Throwable $e) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'امکان ثبت امتیاز در حال حاضر وجود ندارد. لطفاً بعداً تلاش کنید.'],
            ];
        }
    }

    /**
     * Add a comment (multiple comments per user per supplier, each needs approval).
     */
    public function addComment(array $requestBody, array $account): array
    {
        $userId = (int) ($account['id'] ?? 0);
        if ($userId <= 0) {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'Unauthorized']];
        }
        $supplierId = $this->resolveSupplierId($requestBody);
        if ($supplierId <= 0) {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'supplier_id or supplier_slug is required']];
        }
        $comment = isset($requestBody['comment']) ? trim((string) $requestBody['comment']) : '';
        if ($comment === '') {
            return ['result' => false, 'data' => [], 'error' => ['message' => 'comment is required']];
        }
        try {
            $row = $this->reviewRepository->addComment($supplierId, $userId, $comment);
            return ['result' => true, 'data' => $row, 'error' => []];
        } catch (\Throwable $e) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'امکان ثبت نظر در حال حاضر وجود ندارد. لطفاً بعداً تلاش کنید.'],
            ];
        }
    }

    /**
     * Get current user's rating row for a supplier (if any).
     */
    public function getMyRating(array $params, array $account): array
    {
        $userId = (int) ($account['id'] ?? 0);
        if ($userId <= 0) {
            return ['result' => true, 'data' => null, 'error' => []];
        }
        $supplierId = $this->resolveSupplierId($params);
        if ($supplierId <= 0) {
            return ['result' => true, 'data' => null, 'error' => []];
        }
        try {
            $row = $this->reviewRepository->getByUserSupplierType($supplierId, $userId, SupplierReviewRepository::TYPE_RATING);
            return ['result' => true, 'data' => $row, 'error' => []];
        } catch (\Throwable $e) {
            return ['result' => true, 'data' => null, 'error' => []];
        }
    }

    private function resolveSupplierId(array $params): int
    {
        $supplierId = isset($params['supplier_id']) ? (int) $params['supplier_id'] : 0;
        $supplierSlug = isset($params['supplier_slug']) ? trim((string) $params['supplier_slug']) : '';
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

    /**
     * List reviews for a supplier (user/public). Only approved reviews.
     */
    public function getApprovedListBySupplier(array $params): array
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
            return [
                'result' => true,
                'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => 50, 'page' => 1]],
                'error'  => [],
            ];
        }

        $params['supplier_id'] = $supplierId;
        $params['status']      = self::STATUS_APPROVED;
        $params['limit']      = $params['limit'] ?? 50;
        $params['page']       = $params['page'] ?? 1;
        $params['order']      = $params['order'] ?? 'time_create DESC';

        try {
            $data = $this->reviewRepository->getList($params);
            $list = $data['list'] ?? [];
            $this->enrichListWithUserInfo($list);
            $data['list'] = $list;
            if ($supplierId > 0) {
                try {
                    $stats = $this->reviewRepository->getSupplierRatingStats($supplierId);
                    $data['total_rating_sum'] = $stats['total_rating_sum'] ?? 0;
                } catch (\Throwable $e) {
                    $data['total_rating_sum'] = 0;
                }
            }
            return [
                'result' => true,
                'data'   => $data,
                'error'  => [],
            ];
        } catch (\Throwable $e) {
            return [
                'result' => true,
                'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => (int) ($params['limit'] ?? 50), 'page' => (int) ($params['page'] ?? 1)]],
                'error'  => [],
            ];
        }
    }

    /**
     * Admin: list all reviews with optional filters (supplier_id, status).
     */
    public function getListForAdmin(array $params): array
    {
        $params['limit'] = $params['limit'] ?? 50;
        $params['page']  = $params['page'] ?? 1;
        $params['order'] = $params['order'] ?? 'time_create DESC';
        try {
            $data = $this->reviewRepository->getList($params);
        } catch (\Throwable $e) {
            return [
                'result' => true,
                'data'   => ['list' => [], 'paginator' => ['count' => 0, 'limit' => (int) $params['limit'], 'page' => (int) $params['page']]],
                'error'  => [],
            ];
        }
        $list = $data['list'] ?? [];
        $paginator = $data['paginator'] ?? ['count' => 0, 'limit' => 50, 'page' => 1];

        // Enrich with supplier title/slug if needed
        $supplierIds = array_unique(array_filter(array_column($list, 'supplier_id')));
        $supplierTitles = [];
        if ($supplierIds !== []) {
            foreach ($supplierIds as $sid) {
                $s = $this->itemService->getItem((string) $sid, 'id', ['type' => SupplierService::TYPE_SUPPLIER]);
                $supplierTitles[$sid] = [
                    'title' => $s['title'] ?? $s['company_name'] ?? (string) $sid,
                    'slug'  => $s['slug'] ?? '',
                ];
            }
        }
        foreach ($list as &$row) {
            $sid = (int) ($row['supplier_id'] ?? 0);
            $row['supplier_title'] = $supplierTitles[$sid]['title'] ?? '';
            $row['supplier_slug']  = $supplierTitles[$sid]['slug'] ?? '';
        }
        unset($row);
        $this->enrichListWithUserInfo($list);

        return [
            'result' => true,
            'data'   => ['list' => $list, 'paginator' => $paginator],
            'error'  => [],
        ];
    }

    /**
     * Admin: approve or reject a review.
     */
    public function updateStatus(array $requestBody, array $account): array
    {
        $id = isset($requestBody['id']) ? (int) $requestBody['id'] : 0;
        if ($id <= 0) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'id is required'],
            ];
        }
        $status = isset($requestBody['status']) ? trim((string) $requestBody['status']) : '';
        if ($status !== self::STATUS_APPROVED && $status !== self::STATUS_REJECTED) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'status must be approved or rejected'],
            ];
        }

        try {
            $review = $this->reviewRepository->getById($id);
        } catch (\Throwable $e) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'امکان بروزرسانی وضعیت در حال حاضر وجود ندارد.'],
            ];
        }
        if (!$review) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Review not found'],
            ];
        }

        try {
            $ok = $this->reviewRepository->updateStatus($id, $status);
        } catch (\Throwable $e) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'امکان بروزرسانی وضعیت در حال حاضر وجود ندارد.'],
            ];
        }
        if (!$ok) {
            return [
                'result' => false,
                'data'   => [],
                'error'  => ['message' => 'Update failed'],
            ];
        }
        $review['status'] = $status;
        $review['time_update'] = time();
        return [
            'result' => true,
            'data'   => $review,
            'error'  => [],
        ];
    }

    /**
     * Get average rating and review count for one supplier (approved only).
     */
    public function getSupplierRatingStats(int $supplierId): array
    {
        try {
            return $this->reviewRepository->getSupplierRatingStats($supplierId);
        } catch (\Throwable $e) {
            return ['average_rating' => null, 'review_count' => 0, 'total_rating_sum' => 0];
        }
    }

    /**
     * Get rating stats for multiple suppliers (for list with sort by rating).
     *
     * @param array $supplierIds
     * @return array<int, array{average_rating: float|null, review_count: int}>
     */
    public function getRatingStatsBySupplierIds(array $supplierIds): array
    {
        try {
            return $this->reviewRepository->getRatingStatsBySupplierIds($supplierIds);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get supplier IDs ordered by average rating (for list sort by rating).
     *
     * @return array{supplier_ids: int[], total: int}
     */
    public function getSupplierIdsOrderedByRating(int $limit, int $offset): array
    {
        try {
            return $this->reviewRepository->getSupplierIdsOrderedByRating($limit, $offset);
        } catch (\Throwable $e) {
            return ['supplier_ids' => [], 'total' => 0];
        }
    }
}
