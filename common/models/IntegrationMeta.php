<?php


namespace common\models;


use yii\db\Exception;

class IntegrationMeta extends base\BaseIntegrationMeta
{
    /**
     * @throws Exception
     */
    public static function addMeta($key, $value, $id)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $newvalue = base64_encode(\Yii::$app->getSecurity()->encryptByKey(data: $value, inputKey: \Yii::$app->params['encryptionKey']));

        $newMeta = (new IntegrationMeta(['key' => $key, 'value' => $newvalue, 'created_date' => date(format:'Y-m-d H:i:s'), 'integration_id' => $id]));

        if ($newMeta->save(runValidation: true)) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
            var_dump($newMeta->getErrorSummary(showAllErrors: true));
            throw new Exception(message: 'New metadatum could not be saved.');
        }
    }

    /**
     * Returns decrypted value of model
     *
     * @return string
     */
    public function decryptedValue(): string
    {
        return \Yii::$app->getSecurity()->decryptByKey(base64_decode($this->value), \Yii::$app->params['encryptionKey']);
    }
}