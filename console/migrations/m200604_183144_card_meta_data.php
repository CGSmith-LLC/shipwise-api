<?php

use yii\db\Migration;

/**
 * Class m200604_183144_card_meta_data
 */
class m200604_183144_card_meta_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%paymentmethod}}', 'brand', $this->string(64)->null());
        $this->addColumn('{{%paymentmethod}}', 'lastfour', $this->string(64)->null());
        $this->addColumn('{{%paymentmethod}}', 'expiration', $this->string(64)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200604_183144_card_meta_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200604_183144_card_meta_data cannot be reverted.\n";

        return false;
    }
    */
}
