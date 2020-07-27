<?php

use yii\db\Migration;
class m200720_094730_make_state_id_defualt_0 extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%addresses}}','state_id', $this->integer(11)->notNull()->defaultValue(0));
    }
    public function safeDown()
    {
        $this->alterColumn('{{%addresses}}', 'state_id', $this->integer(11)->notNull());
    }
}
