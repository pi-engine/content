<?php

namespace Content\Repository;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Update;
use Laminas\Hydrator\HydratorInterface;
use Content\Model\Item;
use RuntimeException;
use InvalidArgumentException;


class ItemRepository implements ItemRepositoryInterface
{
    /**
     * Item Table name
     *
     * @var string
     */
    private string $tableItem = 'content_item';

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $db;

    /**
     * @var Item
     */
    private Item $itemPrototype;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;
    /**
     * @var mixed
     */
    private $config;

    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Item $itemPrototype
    ) {
        $this->db            = $db;
        $this->hydrator      = $hydrator;
        $this->itemPrototype = $itemPrototype;
    }

    /**
     * @param array $params
     *
     * @return HydratingResultSet|array
     */
    public function getItemList($params): HydratingResultSet|array
    {
        $where = [];
        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $where['user_id'] = $params['user_id'];
        }
        if (isset($params['type']) && !empty($params['type'])) {
            $where['type'] = $params['type'];
        }
        if (isset($params['status']) && !empty($params['status'])) {
            $where['status'] = $params['status'];
        }
        if (isset($params['id']) && !empty($params['id'])) {
            $where['id'] = $params['id'];
        }

        $sql       = new Sql($this->db);
        $select    = $sql->select($this->tableItem)->where($where)->order($params['order'])->offset($params['offset'])->limit($params['limit']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->itemPrototype);
        $resultSet->initialize($result);

        return $resultSet;
    }

    /**
     * @param string $parameter
     * @param string $type
     *
     * @return object|array
     */
    public function getItem($parameter, $type = 'id'): object|array
    {
        $where = [$type => $parameter];

        $sql       = new Sql($this->db);
        $select    = $sql->select($this->tableItem)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException(
                sprintf(
                    'Failed retrieving blog post with identifier "%s"; unknown database error.',
                    $parameter
                )
            );
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->itemPrototype);
        $resultSet->initialize($result);
        $item = $resultSet->current();

        if (!$item) {
            return [];
        }

        return $item;
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function getItemCount(array $params = []): int
    {
        // Set where
        $columns = ['count' => new Expression('count(*)')];
        $where   = [];

        if (isset($params['status']) && is_numeric($params['status'])) {
            $where['status'] = $params['status'];
        }

        $sql       = new Sql($this->db);
        $select    = $sql->select($this->tableItem)->columns($columns)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $row       = $statement->execute()->current();

        return (int)$row['count'];
    }

    /**
     * @param array $params
     *
     * @return array|object $notificationId
     */
    public function addItem($params, $account)
    {
        $insert = new Insert($this->tableItem);
        $insert->values($params);

        $sql       = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result    = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during blog post insert operation'
            );
        }
        $id = $result->getGeneratedValue();
        return $this->getItem($id);
    }

    /**
     * @param array $params
     *
     * @return int $notificationId
     */
    public function editItem($params, $account)
    {
        $update = new Update($this->tableItem);
        $update->set($params);
        $update->where(['id' => $params["id"]]);

        $sql       = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result    = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during update operation'
            );
        }
        return $this->getItem($params);
    }

    /**
     * @param array $params
     *
     * @return int $notificationId
     */
    public function deleteItem($params, $account)
    {
        $update = new Update($this->tableItem);
        $update->set($params);
        $update->where(['id' => $params["id"]]);

        $sql       = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result    = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during update operation'
            );
        }
        return true;
    }
}
