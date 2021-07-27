<?php


namespace common\models\shopify;


use yii\base\BaseObject;
use yii\db\Exception;

class FulfillmentMeta extends \common\models\base\BaseFulfillmentMeta
{
    /**
     * @throws Exception
     */
    public static function addMeta($key, $value, $id)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $newvalue = base64_encode(\Yii::$app->getSecurity()->encryptByKey($value, \Yii::$app->params['encryptionKey']));

        $newMeta = (new FulfillmentMeta(['key' => $key, 'value' => $newvalue, 'created_date' => date('Y-m-d H:i:s'), 'integration_id' => $id]));

        if ($newMeta->save(true)) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
            var_dump($newMeta->getErrorSummary(false));
            throw new Exception('New metadatum could not be saved.');
        }
    }

    /**
     * Returns decrypted value of model
     *
     * @return bool|string
     */
    public function decryptedValue()
    {
        return \Yii::$app->getSecurity()->decryptByKey(base64_decode($this->value), \Yii::$app->params['encryptionKey']);
    }
}