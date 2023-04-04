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
    private string $tableLog = 'content_log';

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
     * @var Log
     */
    private Log $logPrototype;


    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;


    public function __construct(
        AdapterInterface  $db,
        HydratorInterface $hydrator,
        Log               $logPrototype,
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->logPrototype = $logPrototype;
    }


    /**
     * @param string $parameter
     * @param string $type
     *
     * @return object|array
     */
    public function getLog($params): object|array
    {
        $where = [];
        if (isset($params['id']) && !empty($params['id'])) {
            $where['id'] = $params['id'];
        }
        if (isset($params['item_id']) && !empty($params['item_id'])) {
            $where['item_id'] = $params['item_id'];
        }

        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $where['user_id'] = $params['user_id'];
        }

        if (isset($params['action']) && !empty($params['action'])) {
            $where['action'] = $params['action'];
        }

        if (isset($params['time_delete']) ) {
            $where['time_delete'] = $params['time_delete'];
        }

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableLog)->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException(
                sprintf(
                    'Failed retrieving blog post with identifier "%s"; unknown database error.',
                    $params
                )
            );
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->logPrototype);
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
        return $this->getLog(["id" => $id]);
    }
}
