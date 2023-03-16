<?php

use api\modules\v1\models\core\ApiConsumerEx;
use frontend\models\User;
use yii\db\Migration;

/**
 * Class m230221_163449_update_api_consumer
 */
class m230221_163449_update_api_consumer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%api_consumer}}', 'user_id', $this->integer());

        $existingApiConsumers = ApiConsumerEx::find()->all();
        foreach ($existingApiConsumers as $existingApiConsumer) {
            if ($user = User::find()
                ->where(['customer_id' => $existingApiConsumer->customer_id])
                ->one()) {
                $existingApiConsumer->user_id = $user->id;
            } else {
                $existingApiConsumer->user_id = 1;
            }
            $existingApiConsumer->save();
        }

        $this->alterColumn('{{%api_consumer}}', 'user_id', $this->integer()->notNull());

        $this->addForeignKey(
            '{{%fk-api_consumer-user_id}}',
            '{{%api_consumer}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-api_consumer-user_id}}', '{{%api_consumer}}');
        $this->dropColumn('{{%api_consumer}}', 'user_id');
    }

}
