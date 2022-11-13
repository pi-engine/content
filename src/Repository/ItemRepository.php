<?php

namespace Content\Repository;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Update;
use Laminas\Hydrator\HydratorInterface;
use Content\Model\Item\Item;
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
        AdapterInterface  $db,
        HydratorInterface $hydrator,
        Item             $itemPrototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->itemPrototype = $itemPrototype;
    }

    public function getItemList($params)
    {
        $where = ["time_deleted"=>0];
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableItem)->where($where)->order("id DESC")->offset($params['offset'])->limit($params['limit']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving blog post with identifier "%s"; unknown database error.',
                $params
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->itemPrototype);
        $resultSet->initialize($result);
        $resultSet->buffer();

        return $resultSet->toArray();
    }

    public function getItem($params)
    {
        $where = ['id' => $params['id']];
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableItem)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException(sprintf(
                'Failed retrieving blog post with identifier "%s"; unknown database error.',
                $params
            ));
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->itemPrototype);
        $resultSet->initialize($result);
        $resultSet->buffer();

        /// TODO : return a object
        return sizeof($resultSet->toArray()) > 0 ? $resultSet->toArray()[0] : [];
    }

    /**
     * @param array $params
     *
     * @return int $notificationId
     */
    public function storeItem($params)
    {
        $data = $params;

        $insert = new Insert($this->tableItem);
        $insert->values($data);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during blog post insert operation'
            );
        }
        $params['id'] = $result->getGeneratedValue();
        $result = $this->getItem($params);
        return $result;
    }


    /**
     * @param array $params
     *
     * @return int $notificationId
     */
    public function updateItem($params, $account)
    {
        $update = new Update($this->tableItem);
        $update->set($params);
        $update->where(['id' => $params["id"]]);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();

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
    public function deleteItem($params)
    {
        $update = new Update($this->tableItem);
        $update->set($params);
        $update->where(['id' => $params["id"]]);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                'Database error occurred during update operation'
            );
        }
        return true;
    }
}
