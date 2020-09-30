<?php
namespace common\models\query;

use Faker\Provider\Base;
use yii\base\InvalidArgumentException;

class BaseQuery extends \yii\db\ActiveQuery
{
    public $tableName = null;

    /**
     * BaseQuery constructor.
     * @param       $modelClass
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct($modelClass, $config = [])
    {
        parent::__construct($modelClass, $config);

        // Check that we are passing the table name through
        if (is_null($this->tableName)) {
                throw new InvalidArgumentException('Table name is not passed to BaseQuery `Query(get_called_class(), [\'tableName\' => Model::tableName()])`');
        }
    }

    /**
     * Query condition to get orders for given customer id
     *
     * We assume that if $id passed is null then no condition to apply
     *
     * @param int $id Customer Id
     *
     * @return BaseQuery
     */
    public function forCustomer($id)
    {
        return is_numeric($id)
            ? $this->andOnCondition([$this->tableName . '.customer_id' => (int)$id])
            : $this;
    }

    /**
     * Query condition to get orders for multiple given customers
     *
     * @param array $ids Customer Ids
     *
     * @return BaseQuery
     */
    public function forCustomers($ids = [])
    {
        if (!empty($ids)) {
            return $this->andOnCondition([$this->tableName . '.customer_id' => $ids]);
        }
        return $this;
    }

}