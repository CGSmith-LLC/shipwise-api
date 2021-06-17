<?php

use yii\db\Migration;

/**
 * Class m210617_154845_increase_encrypted_size_on_api_table
 */
class m210617_154845_increase_encrypted_size_on_api_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        // Get all consumers
        $apiConsumers = \common\models\ApiConsumer::find()->all();
        $this->dropColumn('{{%api_consumer}}', 'encrypted_secret');

        // iterate and encrypt consumer with encyrption keys
        foreach ($apiConsumers as $apiConsumer) {
            /** @var $apiConsumer \common\models\ApiConsumer */
            $apiConsumer->encrypted_secret = base64_encode(Yii::$app->getSecurity()->encryptByKey($apiConsumer->encrypted_secret, Yii::$app->params['encryptionKey']));
        }

        $this->addColumn('{{%api_consumer}}', 'encrypted_secret',
            $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext'));

        foreach ($apiConsumers as $apiConsumer) {
            $apiConsumer->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->alterColumn('{{%api_consumer}}', 'encrypted_secret', $this->string(128));
    }
}
