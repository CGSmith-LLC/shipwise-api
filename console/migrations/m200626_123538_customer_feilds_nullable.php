<?php

use yii\db\Migration;

/**
 * Class m200626_123538_customer_feilds_nullable
 */
class m200626_123538_customer_feilds_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%customers}}', 'address1', $this->string(64)->null());
        $this->alterColumn('{{%customers}}', 'logo', $this->string(256)->null());
        $this->alterColumn('{{%customers}}', 'zip', $this->string(16)->null());
        $this->alterColumn('{{%customers}}', 'city', $this->string(64)->null());
        $this->alterColumn('{{%customers}}', 'state_id', $this->integer(11)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%customers}}', 'address1', $this->string(64)->null());
        $this->alterColumn('{{%customers}}', 'logo', $this->string(256)->null());
        $this->alterColumn('{{%customers}}', 'zip', $this->string(16)->null());
        $this->alterColumn('{{%customers}}', 'city', $this->string(64)->null());
        $this->alterColumn('{{%customers}}', 'state_id', $this->integer(11)->null());
    }


}
