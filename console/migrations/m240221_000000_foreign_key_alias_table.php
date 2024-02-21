<?php

use yii\db\Migration;

/**
 * Class m230921_062521_add_execution_link_and_last_error
 */
class m240221_000000_foreign_key_alias_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * alter table alias_children
         * add constraint alias_children_alias_parent_id_fk
         * foreign key (alias_id) references alias_parent (id)
         * on delete cascade;
         */

        $this->addForeignKey(
            '{{%fk-alias_children_alias_parent}}',
            '{{%alias_children}}',
            'alias_id',
            '{{%alias_parent}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-alias_children_alias_parent}}','{{%alias_children}}');
    }

}
