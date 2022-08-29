<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%column_manage}}`.
 */
class m220827_163702_create_column_manage_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%column_manage}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'column_data' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->null()->comment('JSON data'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%column_manage}}');
    }
}
