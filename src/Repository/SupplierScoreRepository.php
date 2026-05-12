<?php

declare(strict_types=1);

namespace Content\Repository;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Update;
use Laminas\Db\Sql\Where;

class SupplierScoreRepository
{
    private const TABLE = 'content_supplier_score';

    public const SCORE_MIN = 1;
    public const SCORE_MAX = 5;
    public const STATUS_APPROVED = 'approved';

    public function __construct(
        private AdapterInterface $adapter
    ) {
    }

    /**
     * Insert or update one score. Returns the row.
     */
    public function addOrUpdate(int $supplierId, int $userId, int $scoreTypeId, int $score): array
    {
        $time = time();
        $existing = $this->getByUserSupplierType($supplierId, $userId, $scoreTypeId);
        if ($existing !== null) {
            $update = new Update(self::TABLE);
            $update->set(['score' => $score, 'time_update' => $time]);
            $update->where(['id' => $existing['id']]);
            $sql = new Sql($this->adapter);
            $this->adapter->query($sql->buildSqlString($update), $this->adapter::QUERY_MODE_EXECUTE);
            return [
                'id'            => (int) $existing['id'],
                'supplier_id'   => $supplierId,
                'user_id'       => $userId,
                'score_type_id' => $scoreTypeId,
                'score'         => $score,
                'status'        => $existing['status'] ?? self::STATUS_APPROVED,
                'time_create'   => (int) ($existing['time_create'] ?? $time),
                'time_update'   => $time,
            ];
        }
        $insert = new Insert(self::TABLE);
        $insert->values([
            'supplier_id'   => $supplierId,
            'user_id'       => $userId,
            'score_type_id' => $scoreTypeId,
            'score'         => $score,
            'status'        => self::STATUS_APPROVED,
            'time_create'   => $time,
            'time_update'   => $time,
        ]);
        $sql = new Sql($this->adapter);
        $this->adapter->query($sql->buildSqlString($insert), $this->adapter::QUERY_MODE_EXECUTE);
        $id = (int) $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        return [
            'id'            => $id,
            'supplier_id'   => $supplierId,
            'user_id'       => $userId,
            'score_type_id' => $scoreTypeId,
            'score'         => $score,
            'status'        => 'pending',
            'time_create'   => $time,
            'time_update'   => $time,
        ];
    }

    public function getByUserSupplierType(int $supplierId, int $userId, int $scoreTypeId): ?array
    {
        $select = new Select(self::TABLE);
        $select->columns(['id', 'supplier_id', 'user_id', 'score_type_id', 'score', 'status', 'time_create', 'time_update']);
        $select->where(['supplier_id' => $supplierId, 'user_id' => $userId, 'score_type_id' => $scoreTypeId]);
        $select->limit(1);
        $sql    = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $row    = $result->current();
        return $row ? (array) $row : null;
    }

    /**
     * Get all scores by user for a supplier (for "my scores" form).
     *
     * @return array<int, array{score_type_id: int, score: int, ...}>
     */
    public function getByUserSupplier(int $supplierId, int $userId): array
    {
        $select = new Select(self::TABLE);
        $select->columns(['id', 'supplier_id', 'user_id', 'score_type_id', 'score', 'status', 'time_create', 'time_update']);
        $select->where(['supplier_id' => $supplierId, 'user_id' => $userId]);
        $sql    = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $list = [];
        foreach ($result as $row) {
            $list[(int) $row['score_type_id']] = [
                'id'            => (int) $row['id'],
                'supplier_id'   => (int) $row['supplier_id'],
                'user_id'       => (int) $row['user_id'],
                'score_type_id' => (int) $row['score_type_id'],
                'score'         => (int) $row['score'],
                'status'        => (string) $row['status'],
                'time_create'   => (int) $row['time_create'],
                'time_update'   => (int) $row['time_update'],
            ];
        }
        return $list;
    }

    /**
     * Get average score per score_type for a supplier (approved only).
     *
     * @return array<int, array{score_type_id: int, average_score: float, count: int}>
     */
    public function getAveragesBySupplier(int $supplierId): array
    {
        $select = new Select(self::TABLE);
        $select->columns([
            'score_type_id' => 'score_type_id',
            'average_score' => new Expression('AVG(score)'),
            'count'        => new Expression('COUNT(*)'),
        ]);
        $select->where(['supplier_id' => $supplierId, 'status' => self::STATUS_APPROVED]);
        $select->group('score_type_id');
        $sql    = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $list = [];
        foreach ($result as $row) {
            $list[(int) $row['score_type_id']] = [
                'score_type_id' => (int) $row['score_type_id'],
                'average_score' => round((float) $row['average_score'], 2),
                'count'         => (int) $row['count'],
            ];
        }
        return $list;
    }

    /**
     * Get average score per score_type for multiple suppliers (approved only).
     * Returns [ supplier_id => [ score_type_id => [ average_score, count ], ... ], ... ]
     *
     * @param int[] $supplierIds
     * @return array<int, array<int, array{average_score: float, count: int}>>
     */
    public function getAveragesForSupplierIds(array $supplierIds): array
    {
        if ($supplierIds === []) {
            return [];
        }
        $select = new Select(self::TABLE);
        $select->columns([
            'supplier_id'   => 'supplier_id',
            'score_type_id' => 'score_type_id',
            'average_score' => new Expression('AVG(score)'),
            'count'         => new Expression('COUNT(*)'),
        ]);
        $select->where(['supplier_id' => $supplierIds, 'status' => self::STATUS_APPROVED]);
        $select->group(['supplier_id', 'score_type_id']);
        $sql    = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $bySupplier = [];
        foreach ($result as $row) {
            $sid = (int) $row['supplier_id'];
            $tid = (int) $row['score_type_id'];
            if (!isset($bySupplier[$sid])) {
                $bySupplier[$sid] = [];
            }
            $bySupplier[$sid][$tid] = [
                'average_score' => round((float) $row['average_score'], 2),
                'count'         => (int) $row['count'],
            ];
        }
        return $bySupplier;
    }

    /**
     * Get supplier IDs ordered by average score (overall), for list sort by rating.
     * Only suppliers that have at least one approved score. Ordered by AVG(score) DESC.
     *
     * @return array{supplier_ids: int[], total: int}
     */
    public function getSupplierIdsOrderedByOverallScore(int $limit, int $offset): array
    {
        $sql = new Sql($this->adapter);
        $where = new Where();
        $where->in('status', [self::STATUS_APPROVED, 'pending']);
        $subSelect = new Select(self::TABLE);
        $subSelect->columns([
            'supplier_id' => 'supplier_id',
            'overall'     => new Expression('AVG(score)'),
        ]);
        $subSelect->where($where);
        $subSelect->group('supplier_id');
        $subSelect->order(new Expression('AVG(score) DESC'), 'supplier_id DESC');
        $subSelect->limit($limit);
        $subSelect->offset($offset);

        $result = $this->adapter->query($sql->buildSqlString($subSelect), $this->adapter::QUERY_MODE_EXECUTE);
        $supplierIds = [];
        foreach ($result as $row) {
            $supplierIds[] = (int) $row['supplier_id'];
        }

        $countSelect = new Select(self::TABLE);
        $countSelect->columns(['c' => new Expression('COUNT(DISTINCT supplier_id)')]);
        $countSelect->where($where);
        $countResult = $this->adapter->query($sql->buildSqlString($countSelect), $this->adapter::QUERY_MODE_EXECUTE);
        $total = (int) ($countResult->current()['c'] ?? 0);

        return ['supplier_ids' => $supplierIds, 'total' => $total];
    }
}
