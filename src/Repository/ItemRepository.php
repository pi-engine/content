<?php

namespace Content\Repository;

use Content\Model\Item;
use Content\Model\Key;
use Content\Model\Meta;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Update;
use Laminas\Hydrator\HydratorInterface;
use RuntimeException;
use function sprintf;


class ItemRepository implements ItemRepositoryInterface
{
    /**
     * Item Table name
     *
     * @var string
     */
    private string $tableItem = 'content_item';

    /**
     * Meta Value Table name
     *
     * @var string
     */
    private string $tableMetaValue = 'content_meta_value';

    /**
     * Meta Key Table name
     *
     * @var string
     */
    private string $tableMetaKey = 'content_meta_key';

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $db;

    /**
     * @var Item
     */
    private Item $itemPrototype;

    /**
     * @var Meta
     */
    private Meta $metaValuePrototype;

    /**
     * @var Key
     */
    private Key $metaKeyPrototype;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;


    public function __construct(
        AdapterInterface  $db,
        HydratorInterface $hydrator,
        Item              $itemPrototype,
        Meta              $metaValuePrototype,
        Key               $metaKeyPrototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->itemPrototype = $itemPrototype;
        $this->metaValuePrototype = $metaValuePrototype;
        $this->metaKeyPrototype = $metaKeyPrototype;
    }

    /**
     * @param array $params
     *
     * @return HydratingResultSet|array
     */
    public function getItemList(array $params = []): HydratingResultSet|array
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
        if (isset($params['type']) && !empty($params['type'])) {
            $where['type'] = $params['type'];
        }
        if (isset($params['slug']) && !empty($params['slug'])) {
            $where['slug'] = $params['slug'];
        }

        if (isset($params['data_from']) && !empty($params['data_from'])) {
            $where['time_create >= ?'] = $params['data_from'];
        }
        if (isset($params['data_to']) && !empty($params['data_to'])) {
            $where['time_create <= ?'] = $params['data_to'];
        }

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableItem)->where($where)->order($params['order'])->offset($params['offset'])->limit($params['limit']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->itemPrototype);
        $resultSet->initialize($result);

        return $resultSet;
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
        if (isset($params['type']) && !empty($params['type'])) {
            $where['type'] = $params['type'];
        }
        if (isset($params['slug']) && !empty($params['slug'])) {
            $where['slug'] = $params['slug'];
        }

        if (isset($params['data_from']) && !empty($params['data_from'])) {
            $where['time_create >= ?'] = $params['data_from'];
        }
        if (isset($params['data_to']) && !empty($params['data_to'])) {
            $where['time_create <= ?'] = $params['data_to'];
        }


        $sql = new Sql($this->db);
        $select = $sql->select($this->tableItem)->columns($columns)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $row = $statement->execute()->current();

        return (int)$row['count'];
    }

    /**
     * @param array $params
     *
     * @return array|object
     */
    public function addItem(array $params): object|array
    {
        $insert = new Insert($this->tableItem);
        $insert->values($params);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during blog post insert operation'
            );
        }
        $id = $result->getGeneratedValue();
        return $this->getItem($id);
    }

    /**
     * @param string $parameter
     * @param string $type
     *
     * @return object|array
     */
    public function getItem($parameter, $type = 'id',$params=[]): object|array
    {
        $where = [$type => $parameter];

        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $where['user_id'] = $params['user_id'];
        }

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableItem)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

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
     * @param string $parameter
     * @param string $type
     *
     * @return object|array
     */

    public function getItemFilter($where): object|array
    {

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableItem)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException(
                sprintf(
                    'Failed retrieving blog post with identifier  ; unknown database error.',

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
     * @return array|object
     */
    public function editItem(array $params): object|array
    {
        $update = new Update($this->tableItem);
        $update->set($params);
        if (isset($params["id"]))
            $update->where(['id' => $params["id"]]);

        if (isset($params["slug"]))
            $update->where(['slug' => $params["slug"]]);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during update operation'
            );
        }

        return (isset($params["id"])) ? $this->getItem($params["id"]) : $this->getItem($params["slug"], "slug");
    }

    /**
     * @param array $params
     *
     * @return void
     */
    public function deleteItem(array $params): void
    {
        $update = new Update($this->tableItem);
        $update->set($params);
        $update->where(['id' => $params["id"]]);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }

    /**
     * @param array $params
     *
     * @return void
     */
    public function deleteCart($where): void
    {
        $update = new Delete($this->tableItem);
        $update->where($where);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }

    /**
     * @param array $filters
     *
     * @return HydratingResultSet|array
     */
    // ToDo: This is temp solution, need be improve
    public function getIDFromFilter(array $filters = []): HydratingResultSet|array
    {
        $where = ['status' => 1];
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableMetaValue)->where($where);

        foreach ($filters as $filter) {
            switch ($filter['type']) {
                case 'int':
                    $select->where(['key' => $filter['key'], 'value_number' => $filter['value']]);
                    break;

                case 'string':
                    $select->where(['key' => $filter['key'], 'value_string' => $filter['value']]);
                    break;

                case 'search':
                    $select->where(['key' => $filter['key'], 'value_string like ?' => '%s' . $filter['value'] . '%s']);
                    break;

                case 'rangeMax':
                    $select->where(['key' => $filter['key'], 'value_string < ?' => '%s' . $filter['value'] . '%s']);
                    break;

                case 'rangeMin':
                    $select->where(['key' => $filter['key'], 'value_string > ?' => '%s' . $filter['value'] . '%s']);
                    break;
            }
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->metaValuePrototype);
        $resultSet->initialize($result);

        return $resultSet;
    }


    /**
     * @param array $params
     *
     * @return array|object
     */
    public function addCartItem(array $params): object|array
    {
        $insert = new Insert($this->tableItem);
        $insert->values($params);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during blog post insert operation'
            );
        }
        $id = $result->getGeneratedValue();
        return $this->getItem($id);
    }


    ///     META SECTION
    ///

    /**
     * @param array $params
     *
     * @return HydratingResultSet|array
     */
    public function getMetaValue(array $params = [], $return = "array")
    {
        $where = [];
        if (isset($params['id']) && !empty($params['id'])) {
            $where['id'] = $params['id'];
        }
        if (isset($params['item_id']) && !empty($params['item_id'])) {
            $where['item_id'] = $params['item_id'];
        }
        if (isset($params['meta_key']) && !empty($params['meta_key'])) {
            $where['meta_key'] = $params['meta_key'];
        }

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableMetaValue)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->metaValuePrototype);
        $resultSet->initialize($result);
        $item = $resultSet->current();

        if (!$item) {
            return [];
        }

        return $return == "object" ? $item : $resultSet;

    }

    /**
     * @param array $params
     *
     * @return array|object
     */
    public function addMetaValue(array $params): object|array
    {
        $insert = new Insert($this->tableMetaValue);
        $insert->values($params);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during blog post insert operation'
            );
        }
        $id = $result->getGeneratedValue();
        return $this->getMetaValue(["id" => $id],"object");
    }

    /**
     * @param array $params
     *
     * @return array|object
     */
    public function updateMetaValue(array $params): object|array
    {
        $update = new Update($this->tableMetaValue);
        $update->set($params);
        if (isset($params["id"]))
            $update->where(['id' => $params["id"]]);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during update operation'
            );
        }
        return $this->getMetaValue($params ,"object");
    }


    /**
     * @param string $parameter
     * @param string $type
     *
     * @return object|array
     */
    public function getGroupList($parameter, $type = 'id'): object|array
    {


        $sql = new Sql($this->db);
        $select = $sql->select($this->tableItem)->where("$type IN ($parameter)");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

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


        return $resultSet;
    }

}
