<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%country}}`.
 */
class m200722_181343_create_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%country}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
            'abbreviation' => $this->string(2)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%country}}');
    }
}
