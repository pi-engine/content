<?php

namespace Content\Repository;

use Content\Model\Log;
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


class LogRepository implements LogRepositoryInterface
{
    /**
     * Log Table name
     *
     * @var string
     */
    private string $tableLog = 'content_Log';

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
        Log              $LogPrototype,
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->LogPrototype = $LogPrototype;
    }

    /**
     * @param array $params
     *
     * @return HydratingResultSet|array
     */
    public function getLogList(array $params = []): HydratingResultSet|array
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
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableLog)->where($where)->order($params['order'])->offset($params['offset'])->limit($params['limit']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->LogPrototype);
        $resultSet->initialize($result);

        return $resultSet;
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function getLogCount(array $params = []): int
    {
        // Set where
        $columns = ['count' => new Expression('count(*)')];
        $where = [];

        if (isset($params['status']) && is_numeric($params['status'])) {
            $where['status'] = $params['status'];
        }

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableLog)->columns($columns)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $row = $statement->execute()->current();

        return (int)$row['count'];
    }

    /**
     * @param array $params
     *
     * @return array|object
     */
    public function addLog(array $params): object|array
    {
        $insert = new Insert($this->tableLog);
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
        return $this->getLog($id);
    }

    /**
     * @param string $parameter
     * @param string $type
     *
     * @return object|array
     */
    public function getLog($parameter, $type = 'id'): object|array
    {
        $where = [$type => $parameter];

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableLog)->where($where);
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

        $resultSet = new HydratingResultSet($this->hydrator, $this->LogPrototype);
        $resultSet->initialize($result);
        $Log = $resultSet->current();

        if (!$Log) {
            return [];
        }

        return $Log;
    }

    /**
     * @param string $parameter
     * @param string $type
     *
     * @return object|array
     */

    public function getLogFilter($where): object|array
    {

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableLog)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException(
                sprintf(
                    'Failed retrieving blog post with identifier  ; unknown database error.',

                )
            );
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->LogPrototype);
        $resultSet->initialize($result);
        $Log = $resultSet->current();

        if (!$Log) {
            return [];
        }

        return $Log;
    }

}
