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

        $newkey   = base64_encode(\Yii::$app->getSecurity()->encryptByKey($key,   \Yii::$app->params['integrationSecret']));
        $newvalue = base64_encode(\Yii::$app->getSecurity()->encryptByKey($value, \Yii::$app->params['integrationSecret']));

        $newMeta = (new IntegrationMeta(['key'=>$newkey, 'value'=>$newvalue, 'created_date'=>date('Y-m-d H:i:s'), 'integration_id'=>$id]));

        if($newMeta->save(true)){
            $transaction->commit();
        } else {
            $transaction->rollBack();
            var_dump($newMeta->getErrorSummary(false));
            throw new Exception('New metadatum could not be saved.');
        }
    }
}