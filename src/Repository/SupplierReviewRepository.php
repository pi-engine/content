<?php

declare(strict_types=1);

namespace Content\Repository;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Update;
use Laminas\Db\Sql\Where;

class SupplierReviewRepository
{
    private const TABLE = 'content_supplier_review';

    public const TYPE_RATING = 'rating';
    public const TYPE_COMMENT = 'comment';

    public function __construct(
        private AdapterInterface $adapter
    ) {
    }

    /**
     * Add a review row (legacy: rating+comment in one). Prefer addOrUpdateRating + addComment.
     */
    public function add(int $supplierId, int $userId, int $rating, ?string $comment): array
    {
        $time = time();
        $insert = new Insert(self::TABLE);
        $insert->values([
            'supplier_id'  => $supplierId,
            'user_id'     => $userId,
            'review_type' => self::TYPE_RATING,
            'rating'      => $rating,
            'comment'     => $comment,
            'status'      => 'pending',
            'time_create' => $time,
            'time_update' => $time,
        ]);
        $sql = new Sql($this->adapter);
        $this->adapter->query($sql->buildSqlString($insert), $this->adapter::QUERY_MODE_EXECUTE);
        $id = (int) $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        return [
            'id'          => $id,
            'supplier_id' => $supplierId,
            'user_id'     => $userId,
            'review_type' => self::TYPE_RATING,
            'rating'      => $rating,
            'comment'     => $comment,
            'status'      => 'pending',
            'time_create' => $time,
            'time_update' => $time,
        ];
    }

    /**
     * Get one row by supplier, user and type (for upsert rating).
     */
    public function getByUserSupplierType(int $supplierId, int $userId, string $reviewType): ?array
    {
        $select = new Select(self::TABLE);
        $select->columns(['id', 'supplier_id', 'user_id', 'review_type', 'rating', 'comment', 'status', 'time_create', 'time_update']);
        $select->where(['supplier_id' => $supplierId, 'user_id' => $userId, 'review_type' => $reviewType]);
        $select->limit(1);
        $sql    = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $row    = $result->current();
        return $row ? (array) $row : null;
    }

    /**
     * Insert or update the single rating row for (supplier_id, user_id). Returns the row.
     */
    public function addOrUpdateRating(int $supplierId, int $userId, int $rating): array
    {
        $time = time();
        $existing = $this->getByUserSupplierType($supplierId, $userId, self::TYPE_RATING);
        if ($existing !== null) {
            $update = new Update(self::TABLE);
            $update->set(['rating' => $rating, 'time_update' => $time]);
            $update->where(['id' => $existing['id']]);
            $sql = new Sql($this->adapter);
            $this->adapter->query($sql->buildSqlString($update), $this->adapter::QUERY_MODE_EXECUTE);
            return [
                'id'          => (int) $existing['id'],
                'supplier_id' => $supplierId,
                'user_id'     => $userId,
                'review_type' => self::TYPE_RATING,
                'rating'      => $rating,
                'comment'     => $existing['comment'] ?? null,
                'status'      => $existing['status'] ?? 'pending',
                'time_create' => (int) ($existing['time_create'] ?? $time),
                'time_update' => $time,
            ];
        }
        $insert = new Insert(self::TABLE);
        $insert->values([
            'supplier_id'  => $supplierId,
            'user_id'     => $userId,
            'review_type' => self::TYPE_RATING,
            'rating'      => $rating,
            'comment'     => null,
            'status'      => 'pending',
            'time_create' => $time,
            'time_update' => $time,
        ]);
        $sql = new Sql($this->adapter);
        $this->adapter->query($sql->buildSqlString($insert), $this->adapter::QUERY_MODE_EXECUTE);
        $id = (int) $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        return [
            'id'          => $id,
            'supplier_id' => $supplierId,
            'user_id'     => $userId,
            'review_type' => self::TYPE_RATING,
            'rating'      => $rating,
            'comment'     => null,
            'status'      => 'pending',
            'time_create' => $time,
            'time_update' => $time,
        ];
    }

    /**
     * Add a comment-only row (multiple per user/supplier).
     */
    public function addComment(int $supplierId, int $userId, string $comment): array
    {
        $time = time();
        $insert = new Insert(self::TABLE);
        $insert->values([
            'supplier_id'  => $supplierId,
            'user_id'     => $userId,
            'review_type' => self::TYPE_COMMENT,
            'rating'      => null,
            'comment'     => $comment,
            'status'      => 'pending',
            'time_create' => $time,
            'time_update' => $time,
        ]);
        $sql = new Sql($this->adapter);
        $this->adapter->query($sql->buildSqlString($insert), $this->adapter::QUERY_MODE_EXECUTE);
        $id = (int) $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        return [
            'id'          => $id,
            'supplier_id' => $supplierId,
            'user_id'     => $userId,
            'review_type' => self::TYPE_COMMENT,
            'rating'      => null,
            'comment'     => $comment,
            'status'      => 'pending',
            'time_create' => $time,
            'time_update' => $time,
        ];
    }

    public function getList(array $params): array
    {
        $limit   = (int) ($params['limit'] ?? 50);
        $page    = (int) ($params['page'] ?? 1);
        $offset  = ($page - 1) * $limit;
        $order   = $params['order'] ?? 'time_create DESC';

        $select = new Select(self::TABLE);
        $select->columns(['id', 'supplier_id', 'user_id', 'review_type', 'rating', 'comment', 'status', 'time_create', 'time_update']);

        $where = new Where();
        if (!empty($params['supplier_id'])) {
            $where->equalTo('supplier_id', (int) $params['supplier_id']);
        }
        if (!empty($params['status'])) {
            $where->equalTo('status', (string) $params['status']);
        }
        if (isset($params['user_id'])) {
            $where->equalTo('user_id', (int) $params['user_id']);
        }
        if (!empty($params['review_type'])) {
            $where->equalTo('review_type', (string) $params['review_type']);
        }
        if ($where->count() > 0) {
            $select->where($where);
        }

        $select->order($order);
        $select->limit($limit);
        $select->offset($offset);

        $sql    = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $rows   = $result->toArray();

        // count total
        $countSelect = new Select(self::TABLE);
        $countSelect->columns(['c' => new \Laminas\Db\Sql\Expression('COUNT(*)')]);
        if ($where->count() > 0) {
            $countSelect->where($where);
        }
        $countResult = $this->adapter->query($sql->buildSqlString($countSelect), $this->adapter::QUERY_MODE_EXECUTE);
        $countRow    = $countResult->current();
        $total       = $countRow ? (int) $countRow['c'] : 0;

        return [
            'list'     => $rows,
            'paginator' => ['count' => $total, 'limit' => $limit, 'page' => $page],
        ];
    }

    public function getById(int $id): ?array
    {
        $select = new Select(self::TABLE);
        $select->columns(['id', 'supplier_id', 'user_id', 'review_type', 'rating', 'comment', 'status', 'time_create', 'time_update']);
        $select->where(['id' => $id]);
        $sql    = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $row    = $result->current();
        return $row ? (array) $row : null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $update = new Update(self::TABLE);
        $update->set(['status' => $status, 'time_update' => time()]);
        $update->where(['id' => $id]);
        $sql    = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($update), $this->adapter::QUERY_MODE_EXECUTE);
        return $result->getAffectedRows() > 0;
    }

    /**
     * Get average rating, count and total rating sum for a supplier (approved rating rows only).
     */
    public function getSupplierRatingStats(int $supplierId): array
    {
        $sql = new Sql($this->adapter);
        $select = new Select(self::TABLE);
        $select->columns([
            'avg_rating' => new \Laminas\Db\Sql\Expression('AVG(rating)'),
            'count'      => new \Laminas\Db\Sql\Expression('COUNT(*)'),
            'total_sum'  => new \Laminas\Db\Sql\Expression('COALESCE(SUM(rating), 0)'),
        ]);
        $where = new Where();
        $where->equalTo('supplier_id', $supplierId)->equalTo('status', 'approved')
            ->equalTo('review_type', self::TYPE_RATING);
        $select->where($where);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        if (!$row || (int) $row['count'] === 0) {
            return ['average_rating' => null, 'review_count' => 0, 'total_rating_sum' => 0];
        }
        return [
            'average_rating'  => round((float) $row['avg_rating'], 2),
            'review_count'    => (int) $row['count'],
            'total_rating_sum' => (int) $row['total_sum'],
        ];
    }

    /**
     * Total sum of ratings for a supplier (approved, rating type only).
     */
    public function getSupplierTotalRatingSum(int $supplierId): int
    {
        $stats = $this->getSupplierRatingStats($supplierId);
        return (int) ($stats['total_rating_sum'] ?? 0);
    }

    /**
     * Get average rating per supplier for many supplier IDs (for list sort).
     *
     * @param int[] $supplierIds
     * @return array<int, array{average_rating: float|null, review_count: int}>
     */
    public function getRatingStatsBySupplierIds(array $supplierIds): array
    {
        if ($supplierIds === []) {
            return [];
        }
        $sql = new Sql($this->adapter);
        $select = new Select(self::TABLE);
        $select->columns([
            'supplier_id',
            'avg_rating' => new \Laminas\Db\Sql\Expression('AVG(rating)'),
            'count'      => new \Laminas\Db\Sql\Expression('COUNT(*)'),
            'total_sum'  => new \Laminas\Db\Sql\Expression('COALESCE(SUM(rating), 0)'),
        ]);
        $where = new Where();
        $where->in('supplier_id', array_map('intval', $supplierIds))
            ->equalTo('status', 'approved')
            ->equalTo('review_type', self::TYPE_RATING);
        $select->where($where);
        $select->group('supplier_id');
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $out = [];
        foreach ($result as $row) {
            $sid = (int) $row['supplier_id'];
            $out[$sid] = [
                'average_rating'   => (float) $row['avg_rating'],
                'review_count'     => (int) $row['count'],
                'total_rating_sum' => (int) $row['total_sum'],
            ];
        }
        return $out;
    }

    /**
     * Get supplier IDs ordered by average rating (approved only), for pagination.
     * Returns suppliers that have at least one approved review, ordered by avg rating desc.
     *
     * @return array{supplier_ids: int[], total: int}
     */
    public function getSupplierIdsOrderedByRating(int $limit, int $offset): array
    {
        $sql = new Sql($this->adapter);
        $select = new Select(self::TABLE);
        $select->columns(['supplier_id' => new \Laminas\Db\Sql\Expression('supplier_id')]);
        $where = new Where();
        $where->equalTo('status', 'approved')->equalTo('review_type', self::TYPE_RATING);
        $select->where($where);
        $select->group('supplier_id');
        $select->order(new \Laminas\Db\Sql\Expression('AVG(rating) DESC'), 'supplier_id DESC');
        $select->limit($limit);
        $select->offset($offset);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $supplierIds = [];
        foreach ($result as $row) {
            $supplierIds[] = (int) $row['supplier_id'];
        }

        $countSelect = new Select(self::TABLE);
        $countSelect->columns(['c' => new \Laminas\Db\Sql\Expression('COUNT(DISTINCT supplier_id)')]);
        $countWhere = new Where();
        $countWhere->equalTo('status', 'approved')->equalTo('review_type', self::TYPE_RATING);
        $countSelect->where($countWhere);
        $countResult = $this->adapter->query($sql->buildSqlString($countSelect), $this->adapter::QUERY_MODE_EXECUTE);
        $total = (int) ($countResult->current()['c'] ?? 0);

        return ['supplier_ids' => $supplierIds, 'total' => $total];
    }
}
