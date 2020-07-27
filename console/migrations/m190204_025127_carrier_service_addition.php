<?php

use yii\db\Migration;

/**
 * Class m190204_025127_carrier_service_addition
 */
class m190204_025127_carrier_service_addition extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%orders}}', 'carrier_id', $this->smallInteger()->null());
        $this->addColumn('{{%orders}}', 'service_id', $this->smallInteger()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'carrier_id');
        $this->dropColumn('{{%orders}}', 'service_id');
    }

}
