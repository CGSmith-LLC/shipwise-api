<?php

use yii\db\Migration;

/**
 * Class m191023_014053_customer_meta_data
 */
class m191023_014053_customer_meta_data extends Migration
{
    /** {@inheritdoc} */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%customers_meta}}', [
            'id'             => $this->primaryKey(),
            'customer_id'    => $this->integer(11)->defaultValue(null),
            'key'            => $this->string(64)->notNull()
                ->comment('Customer key defining a variable'),
            'value'          => $this->string(128)->notNull()
                ->comment('Customer value assigning the variable'),
            'created_date'       => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions . " COMMENT 'List of customer meta data'");

        $this->createIndex('customer_meta_key_idx', '{{%customers_meta}}', 'key');
    }

    /** {@inheritdoc} */
    public function safeDown()
    {
        $this->dropTable('{{%customers_meta}}');
    }
}
