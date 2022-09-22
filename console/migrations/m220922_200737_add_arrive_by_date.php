<?php

use yii\db\Migration;

/**
 * Class m220922_200737_add_arrive_by_date
 */
class m220922_200737_add_arrive_by_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'must_arrive_by_date', $this->dateTime()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'must_arrive_by_date');

    }

}
