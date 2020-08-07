<?php

use yii\db\Migration;

/**
 * Class m181027_182316_add_superuser
 */
class m181027_182316_add_superuser extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%api_consumer}}', 'superuser',
            $this->smallInteger()->notNull()->defaultValue(0)
                ->comment('API superuser status. 1:active, 0:inactive'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%api_consumer}}', 'superuser');
    }

}
