<?php

use yii\db\Migration;

/**
 * Class m220921_232826_behavior_meta
 */
class m220921_232826_behavior_meta extends Migration
{
    /** {@inheritdoc} */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%behavior_meta}}', [
            'id'             => $this->primaryKey(),
            'customer_id'    => $this->integer(11)->defaultValue(null),
            'key'            => $this->string(128)->notNull()->comment('Behavior key defining a variable'),
            'value'          => $this->string(128)->notNull()->comment('Behavior value assigning the variable'),
            'created_date'   => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions . " COMMENT 'List of behavior meta data'");

    }

    /** {@inheritdoc} */
    public function safeDown()
    {
        $this->dropTable('{{%behavior_meta}}');
    }
}
