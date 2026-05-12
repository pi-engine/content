<?php

declare(strict_types=1);

namespace Content\Repository;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;

class SupplierScoreTypeRepository
{
    private const TABLE = 'content_score_type';

    public function __construct(
        private AdapterInterface $adapter
    ) {
    }

    /**
     * @return list<array{id: int, key: string, title_fa: string, title_en: string, sort_order: int, status: int}>
     */
    public function getActiveList(): array
    {
        $select = new Select(self::TABLE);
        $select->columns(['id', 'key', 'title_fa', 'title_en', 'sort_order', 'status']);
        $select->where(['status' => 1]);
        $select->order('sort_order ASC');
        $sql   = new Sql($this->adapter);
        $result = $this->adapter->query($sql->buildSqlString($select), $this->adapter::QUERY_MODE_EXECUTE);
        $list = [];
        foreach ($result as $row) {
            $list[] = [
                'id'         => (int) $row['id'],
                'key'        => (string) $row['key'],
                'title_fa'   => (string) $row['title_fa'],
                'title_en'   => (string) $row['title_en'],
                'sort_order' => (int) $row['sort_order'],
                'status'     => (int) $row['status'],
            ];
        }
        return $list;
    }
}
