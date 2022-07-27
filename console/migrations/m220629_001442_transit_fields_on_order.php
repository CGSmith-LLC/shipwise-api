<?php

use yii\db\Migration;

/**
 * Class m220629_001442_transit_fields_on_order
 */
class m220629_001442_transit_fields_on_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%orders}}', 'transit', $this->integer(11)->null());
        $this->addColumn('{{%orders}}', 'packagingNotes', $this->string(64)->null());
        $this->addColumn('{{%items}}', 'type', $this->string(64)->null());
        $this->addColumn('{{%api_consumer}}', 'facility_id', $this->smallInteger()->notNull()->defaultValue(0));
        $this->addColumn('{{%user}}', 'facility_id', $this->integer(11)
            ->notNull()
            ->defaultValue(0)
            ->comment('User is associated with this facility if it is present'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%orders}}', 'transit');
        $this->dropColumn('{{%orders}}', 'packagingNotes');
        $this->dropColumn('{{%items}}', 'type');
    }
}
