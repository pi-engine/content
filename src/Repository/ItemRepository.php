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

        $where = $this->createConditional($params);

        ///TODO: kerloper: move fom here
        /// support filter section
        if (isset($params['support_follow_up_date']) && !empty($params['support_follow_up_date'])) {
            $where[] = new Expression("JSON_EXTRACT(information, '$.follow_up_date') LIKE ?", '%' . $params['support_follow_up_date'] . '%');
        }

        if (isset($params['support_title']) && !empty($params['support_title'])) {
            $where[] = new Expression("LOWER(JSON_EXTRACT(information, '$.title')) LIKE ?", '%' . strtolower($params['support_title']) . '%');
        }

        if (isset($params['support_product_title']) && !empty($params['support_product_title'])) {
            $where[] = new Expression("LOWER(JSON_EXTRACT(information, '$.order.product.title')) LIKE ?", '%' . strtolower($params['support_product_title']) . '%');
        }

        if (isset($params['support_customer_name']) && !empty($params['support_customer_name'])) {
            $where[] = new Expression("LOWER(JSON_EXTRACT(information, '$.customer.name')) LIKE ?", '%' . strtolower($params['support_customer_name']) . '%');
        }

        if (isset($params['support_customer_email']) && !empty($params['support_customer_email'])) {
            $where[] = new Expression("LOWER(JSON_EXTRACT(information, '$.customer.email')) LIKE ?", '%' . strtolower($params['support_customer_email']) . '%');
        }

        if (isset($params['support_customer_id']) && !empty($params['support_customer_id'])) {
            $where[] = new Expression("LOWER(JSON_EXTRACT(information, '$.customer.id')) IN ( ? ) ", strtolower($params['support_customer_id']));
        }

        if (isset($params['support_order_status']) && !empty($params['support_order_status'])) {
            $where[] = new Expression("JSON_EXTRACT(information, '$.order.order_status') LIKE ?", '%' . $params['support_order_status'] . '%');
        }

        if (isset($params['company_id']) && (int) $params['company_id'] > 0 && isset($params['type']) && $params['type'] === 'material_offer') {
            $where[] = new Expression("JSON_EXTRACT(information, '$.company_id') = ?", [(int) $params['company_id']]);
        }
        if (isset($params['type']) && $params['type'] === 'material_offer' && isset($params['offer_status']) && trim((string) $params['offer_status']) !== '') {
            $where[] = new Expression("JSON_UNQUOTE(JSON_EXTRACT(information, '$.status')) = ?", [trim((string) $params['offer_status'])]);
        }
        if (isset($params['type']) && $params['type'] === 'material_offer' && isset($params['country_of_origin']) && trim((string) $params['country_of_origin']) !== '') {
            $search = '%' . mb_strtolower(trim((string) $params['country_of_origin'])) . '%';
            $where[] = new Expression(
                "LOWER(CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(information, '$.country_of_origin')), '') AS CHAR(500))) LIKE ?",
                [$search]
            );
        }

        // Supplier list tabs: active | pending | inactive (must match getItemCount)
        if (isset($params['type']) && $params['type'] === 'supplier' && isset($params['list_type']) && in_array($params['list_type'], ['active', 'pending', 'inactive'], true)) {
            $listType = $params['list_type'];
            if ($listType === 'pending') {
                $where[] = new Expression(
                    "JSON_UNQUOTE(JSON_EXTRACT(information, '$.registration_source')) = ? AND (JSON_EXTRACT(information, '$.was_activated_at') IS NULL)",
                    ['public']
                );
            } elseif ($listType === 'inactive') {
                $where[] = new Expression(
                    "JSON_UNQUOTE(COALESCE(JSON_EXTRACT(information, '$.supplier_active'), '1')) = ?",
                    ['0']
                );
            } else {
                $where[] = new Expression(
                    "((JSON_EXTRACT(information, '$.supplier_active') IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(information, '$.supplier_active')) != ?) AND (JSON_EXTRACT(information, '$.registration_source') IS NULL OR JSON_UNQUOTE(COALESCE(JSON_EXTRACT(information, '$.registration_source'), '\"\"')) != ? OR JSON_EXTRACT(information, '$.was_activated_at') IS NOT NULL))",
                    ['0', 'public']
                );
            }
        }

        if (!empty($params['parent_id']) && is_array($params['parent_id'])) {
            $ids = array_values(array_filter(array_map('intval', $params['parent_id'])));
            if ($ids !== []) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $where[] = new Expression('parent_id IN (' . $placeholders . ')', $ids);
            }
        }

        // sub_industries_keys: Material uses $.sub_industries_keys; Supplier uses industry_subindustries[].sub_industry.slug
        if (!empty($params['sub_industries_keys']) && is_array($params['sub_industries_keys'])) {
            $keys = array_values(array_filter(array_map('trim', $params['sub_industries_keys'])));
            if ($keys !== []) {
                if (isset($params['type']) && $params['type'] === 'supplier') {
                    // Supplier: match industry_subindustries[].sub_industry.slug (key = slug or last part, e.g. "plastics_and_polymers" or "meta-industry-chemical-plastics_and_polymers")
                    $conditions = [];
                    $bind = [];
                    foreach ($keys as $key) {
                        $esc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $key);
                        $conditions[] = "(information LIKE ? OR information LIKE ? OR information LIKE ?)";
                        $bind[] = '%"sub_industry":%"slug":"' . $esc . '"%';
                        $bind[] = '%"sub_industry":%"slug": "' . $esc . '"%';
                        $bind[] = '%"sub_industry":%-' . $esc . '"%';
                    }
                    $where[] = new Expression('(' . implode(' OR ', $conditions) . ')', $bind);
                } else {
                    // Material: match $.sub_industries_keys
                    $placeholders = implode(' OR ', array_fill(0, count($keys), "JSON_CONTAINS(information, ?, '$.sub_industries_keys')"));
                    $where[] = new Expression('(' . $placeholders . ')', array_map('json_encode', $keys));
                }
            }
        }

        // Supplier filter: by sub_industry id(s) (information.industry_subindustries[].sub_industry.id)
        if (!empty($params['sub_industry_ids']) && is_array($params['sub_industry_ids'])) {
            $ids = array_values(array_filter(array_map('intval', $params['sub_industry_ids'])));
            if ($ids !== []) {
                $conditions = [];
                $bind = [];
                foreach ($ids as $sid) {
                    if ($sid > 0) {
                        $conditions[] = '(information LIKE ? OR information LIKE ?)';
                        $bind[] = '%"sub_industry":{"id":' . $sid . '%';
                        $bind[] = '%"sub_industry": {"id": ' . $sid . '%';
                    }
                }
                if ($conditions !== []) {
                    $where[] = new Expression('(' . implode(' OR ', $conditions) . ')', $bind);
                }
            }
        } elseif (!empty($params['sub_industry_id']) && (int) $params['sub_industry_id'] > 0) {
            $sid = (int) $params['sub_industry_id'];
            $where[] = new Expression(
                '(information LIKE ? OR information LIKE ?)',
                ['%"sub_industry":{"id":' . $sid . '%', '%"sub_industry": {"id": ' . $sid . '%']
            );
        }

        $order = $params['order'] ?? ['time_create DESC', 'id DESC'];
        if (isset($params['type']) && $params['type'] === 'material_offer' && !empty($params['order_by_price_rial'])) {
            $dir = (strtolower((string) $params['order_by_price_rial']) === 'asc') ? 'ASC' : 'DESC';
            $order = [new Expression('(CAST(JSON_UNQUOTE(JSON_EXTRACT(information, "$.unit_price_rial")) AS UNSIGNED)) ' . $dir), 'id DESC'];
        }
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableItem)->where($where)->order($order)->offset($params['offset'])->limit($params['limit']);
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

        $where = $this->createConditional($params);

        if (isset($params['company_id']) && (int) $params['company_id'] > 0 && isset($params['type']) && $params['type'] === 'material_offer') {
            $where[] = new Expression("JSON_EXTRACT(information, '$.company_id') = ?", [(int) $params['company_id']]);
        }
        if (isset($params['type']) && $params['type'] === 'material_offer' && isset($params['offer_status']) && trim((string) $params['offer_status']) !== '') {
            $where[] = new Expression("JSON_UNQUOTE(JSON_EXTRACT(information, '$.status')) = ?", [trim((string) $params['offer_status'])]);
        }
        if (isset($params['type']) && $params['type'] === 'material_offer' && isset($params['country_of_origin']) && trim((string) $params['country_of_origin']) !== '') {
            $search = '%' . mb_strtolower(trim((string) $params['country_of_origin'])) . '%';
            $where[] = new Expression(
                "LOWER(CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(information, '$.country_of_origin')), '') AS CHAR(500))) LIKE ?",
                [$search]
            );
        }

        if (isset($params['type']) && $params['type'] === 'supplier' && isset($params['list_type']) && in_array($params['list_type'], ['active', 'pending', 'inactive'], true)) {
            $listType = $params['list_type'];
            if ($listType === 'pending') {
                $where[] = new Expression(
                    "JSON_UNQUOTE(JSON_EXTRACT(information, '$.registration_source')) = ? AND (JSON_EXTRACT(information, '$.was_activated_at') IS NULL)",
                    ['public']
                );
            } elseif ($listType === 'inactive') {
                $where[] = new Expression(
                    "JSON_UNQUOTE(COALESCE(JSON_EXTRACT(information, '$.supplier_active'), '1')) = ?",
                    ['0']
                );
            } else {
                $where[] = new Expression(
                    "((JSON_EXTRACT(information, '$.supplier_active') IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(information, '$.supplier_active')) != ?) AND (JSON_EXTRACT(information, '$.registration_source') IS NULL OR JSON_UNQUOTE(COALESCE(JSON_EXTRACT(information, '$.registration_source'), '\"\"')) != ? OR JSON_EXTRACT(information, '$.was_activated_at') IS NOT NULL))",
                    ['0', 'public']
                );
            }
        } elseif (isset($params['type']) && $params['type'] === 'supplier' && isset($params['registration_source']) && trim((string) $params['registration_source']) !== '') {
            $src = trim((string) $params['registration_source']);
            if ($src === 'public') {
                $where[] = new Expression(
                    "JSON_UNQUOTE(JSON_EXTRACT(information, '$.registration_source')) = ? AND (JSON_EXTRACT(information, '$.was_activated_at') IS NULL)",
                    ['public']
                );
            } elseif ($src === 'exclude_public') {
                $where[] = new Expression(
                    "(JSON_EXTRACT(information, '$.registration_source') IS NULL OR JSON_UNQUOTE(COALESCE(JSON_EXTRACT(information, '$.registration_source'), '\"\"')) != ? OR JSON_EXTRACT(information, '$.was_activated_at') IS NOT NULL)",
                    ['public']
                );
            }
        }

        if (!empty($params['parent_id']) && is_array($params['parent_id'])) {
            $ids = array_values(array_filter(array_map('intval', $params['parent_id'])));
            if ($ids !== []) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $where[] = new Expression('parent_id IN (' . $placeholders . ')', $ids);
            }
        }

        if (!empty($params['sub_industries_keys']) && is_array($params['sub_industries_keys'])) {
            $keys = array_values(array_filter(array_map('trim', $params['sub_industries_keys'])));
            if ($keys !== []) {
                if (isset($params['type']) && $params['type'] === 'supplier') {
                    $conditions = [];
                    $bind = [];
                    foreach ($keys as $key) {
                        $esc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $key);
                        $conditions[] = "(information LIKE ? OR information LIKE ? OR information LIKE ?)";
                        $bind[] = '%"sub_industry":%"slug":"' . $esc . '"%';
                        $bind[] = '%"sub_industry":%"slug": "' . $esc . '"%';
                        $bind[] = '%"sub_industry":%-' . $esc . '"%';
                    }
                    $where[] = new Expression('(' . implode(' OR ', $conditions) . ')', $bind);
                } else {
                    $placeholders = implode(' OR ', array_fill(0, count($keys), "JSON_CONTAINS(information, ?, '$.sub_industries_keys')"));
                    $where[] = new Expression('(' . $placeholders . ')', array_map('json_encode', $keys));
                }
            }
        }

        if (!empty($params['sub_industry_ids']) && is_array($params['sub_industry_ids'])) {
            $ids = array_values(array_filter(array_map('intval', $params['sub_industry_ids'])));
            if ($ids !== []) {
                $conditions = [];
                $bind = [];
                foreach ($ids as $sid) {
                    if ($sid > 0) {
                        $conditions[] = '(information LIKE ? OR information LIKE ?)';
                        $bind[] = '%"sub_industry":{"id":' . $sid . '%';
                        $bind[] = '%"sub_industry": {"id": ' . $sid . '%';
                    }
                }
                if ($conditions !== []) {
                    $where[] = new Expression('(' . implode(' OR ', $conditions) . ')', $bind);
                }
            }
        } elseif (!empty($params['sub_industry_id']) && (int) $params['sub_industry_id'] > 0) {
            $sid = (int) $params['sub_industry_id'];
            $where[] = new Expression(
                '(information LIKE ? OR information LIKE ?)',
                ['%"sub_industry":{"id":' . $sid . '%', '%"sub_industry": {"id": ' . $sid . '%']
            );
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
     * @return int
     */
    public function getMetaKeyCount(array $params = []): int
    {
        $where = [];
        if (isset($params['target']) && !empty($params['target'])) {
            $where['target'] = $params['target'];
        }
        // Set where
        $columns = ['count' => new Expression('count(*)')];
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableMetaKey)->columns($columns)->where($where);
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
    public function getItem($parameter, $type = 'id', $params = []): object|array
    {
        $where = [$type => $parameter];

        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $where['user_id'] = $params['user_id'];
        }
        if (isset($params['type']) && !empty($params['type'])) {
            $where['type'] = $params['type'];
        }
        if (isset($params['status']) && !empty($params['status'])) {
            $where['status'] = $params['status'];
        } else {
            $where['status'] = [0, 1];
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
    public function destroyItem($where): void
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
    public function getIDFromFilter_old(array $filters = []): HydratingResultSet|array
    {
        $where = ['status' => 1];
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableMetaValue)->where($where);

        foreach ($filters as $filter) {
            switch ($filter['type']) {
                case 'id':
                    $select->where(['meta_key' => $filter['key'], 'value_id' => $filter['value']]);
                    break;

                case 'int':
                    $select->where(['meta_key' => $filter['meta_key'], 'value_number' => $filter['value']]);
                    break;

                case 'string':
                    $select->where(['meta_key' => $filter['meta_key'], 'value_string' => $filter['value']]);
                    break;

                case 'search':
                    $select->where(['meta_key' => $filter['meta_key'], 'value_string like ?' => '%s' . $filter['value'] . '%s']);
                    break;

                case 'rangeMax':
                    $select->where(['meta_key' => $filter['meta_key'], 'value_string < ?' => '%s' . $filter['value'] . '%s']);
                    break;
                case 'slug':
                    $select->where(['meta_key' => $filter['meta_key'], 'value_slug' => $filter['value']]);
                    break;

                case 'rangeMin':
                    $select->where(['meta_key' => $filter['meta_key'], 'value_string > ?' => '%s' . $filter['value'] . '%s']);
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

    public function getIDFromFilter(array $filter = []): HydratingResultSet|array
    {
        $where = ['status' => 1];
        $sql = new Sql($this->db);
        $select = $sql->select($this->tableMetaValue)->where($where);


        switch ($filter['type']) {
            case 'id':
                $select->where(['meta_key' => $filter['key'], 'value_id' => $filter['value']]);
                break;

            case 'int':
                $select->where(['meta_key' => $filter['meta_key'], 'value_number' => $filter['value']]);
                break;

            case 'string':
                $select->where(['meta_key' => $filter['meta_key'], 'value_string' => $filter['value']]);
                break;

            case 'search':
                $select->where(['meta_key' => $filter['meta_key'], 'value_string like ?' => '%s' . $filter['value'] . '%s']);
                break;

            case 'slug':
                $select->where(['meta_key' => $filter['meta_key'], 'value_slug' => $filter['value']]);
                break;

            case 'rangeMax':
                $select->where(['meta_key' => $filter['meta_key'], 'value_number < ?' => (int)$filter['value']]);
                break;
            case 'rangeMin':
                $select->where(['meta_key' => $filter['meta_key'], 'value_number >  ?' => (int)$filter['value']]);
                break;
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
    public function addMetaKey(array $params): object|array
    {
        $insert = new Insert($this->tableMetaKey);
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
        return $this->getMetaKey(["id" => $id]);
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
        return $this->getMetaValue(["id" => $id]);
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
        return $this->getMetaValue($params, "object");
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

    private function createConditional(array $params): array
    {
        $where = [];
        if (isset($params['title']) && !empty($params['title'])) {
            $where['title LIKE ?'] = '%' . $params['title'] . '%';
        }
        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $where['user_id'] = $params['user_id'];
        }
        if (array_key_exists('parent_id', $params)) {
            if (is_array($params['parent_id']) && !empty($params['parent_id'])) {
                // Handled in getItemList/getItemCount with Expression
            } else {
                $where['parent_id'] = (int) $params['parent_id'];
            }
        }
        if (isset($params['type']) && !empty($params['type'])) {
            $where['type'] = $params['type'];
        }
        if (isset($params['status']) && in_array($params['status'], [0, 1])) {
            $where['status'] = $params['status'];
        }
        if (isset($params['id'])) {
            if (!empty($params['id'])) {
//                $where['id IN (?) '] =implode("','", $params['id']);
                $where['id'] = $params['id'];
            } else {

                $where['id IN (?) '] = -1;
            }
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
        return $where;
    }


    // Meta repo

    /**
     * @param array $params
     *
     * @return HydratingResultSet|array
     */
    public function getMetaKeyList(array $params = []): HydratingResultSet|array
    {

        $where = [];
        $where['status'] = 1;
        if (isset($params['target']) && !empty($params['target'])) {
            $where['target'] = $params['target'];
        }

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableMetaKey)->where($where)->order($params['order'])->offset($params['offset'])->limit($params['limit']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return [];
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->metaKeyPrototype);
        $resultSet->initialize($result);

        return $resultSet;
    }

    /**
     * @param array $params
     *
     * @return object
     */
    public function getMetaKey(array $params = []): object
    {
        $where = [];
        if (isset($params['key']) && !empty($params['key'])) {
            $where['key'] = $params['key'];
        }
        if (isset($params['id']) && !empty($params['id'])) {
            $where['id'] = $params['id'];
        }
        if (isset($params['target']) && !empty($params['target'])) {
            $where['target'] = $params['target'];
        }

        $sql = new Sql($this->db);
        $select = $sql->select($this->tableMetaKey)->where($where);
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

        $resultSet = new HydratingResultSet($this->hydrator, $this->metaKeyPrototype);
        $resultSet->initialize($result);
        $item = $resultSet->current();

        if (!$item) {
            return [];
        }

        return $item;
    }

    public function destroyMetaValue($where): void
    {
        $update = new Delete($this->tableMetaValue);
        $update->where($where);

        $sql = new Sql($this->db);
        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();
    }
    /**
     * @param array $params
     *
     * @return array|object
     */
    public function updateMetaKey(array $params): object|array
    {
        $update = new Update($this->tableMetaKey);
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

}
