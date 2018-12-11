<?php

use yii\db\Migration;

/**
 * Class m181211_141338_ship_to_email_addition
 */
class m181211_141338_ship_to_email_addition extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%addresses}}', 'email',
            $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%addresses}}', 'email');
    }

}
