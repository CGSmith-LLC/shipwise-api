<?php

use yii\db\Migration;

/**
 * Class m200727_002352_alter_order_table
 */
class m200727_002352_alter_order_table extends Migration
{

    public $tableName = '{{%orders}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->tableName,
            'label_data',
            $this
                ->getDb()->getSchema()->createColumnSchemaBuilder(
                    'mediumtext'
                )
                ->defaultValue(null)
                ->comment('Shipping labels file encoded in base64')
        );

        $this->addColumn(
            $this->tableName,
            'label_type',
            $this
                ->string(6)
                ->defaultValue(null)
                ->comment('File type for label_data field.')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'label_data');
        $this->dropColumn($this->tableName, 'label_type');
    }

}
