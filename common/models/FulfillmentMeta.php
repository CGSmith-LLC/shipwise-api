<?php


namespace common\models;


use common\models\base\BaseFulfillmentMeta;
use yii\db\Exception;

class FulfillmentMeta extends BaseFulfillmentMeta
{
    /**
     * @throws Exception
     */
    public static function addMeta($key, $value, $id)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $newvalue = base64_encode(\Yii::$app->getSecurity()->encryptByKey($value, \Yii::$app->params['encryptionKey']));

        $newMeta = (new FulfillmentMeta(['key' => $key, 'value' => $newvalue, 'created_date' => date('Y-m-d H:i:s'), 'fulfillment_id' => $id]));

        if ($newMeta->save(runValidation: true)) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
            var_dump($newMeta->getErrorSummary(showAllErrors: false));
            throw new Exception(message: 'New metadatum could not be saved.' . PHP_EOL);
        }
    }

	/**
	 * @throws \yii\db\StaleObjectException
	 * @throws \Throwable
	 * @throws Exception
	 */
	public function updateMeta($newkey = null, $newval = null)
	{
		if(!is_null($newkey))
		{
			$transaction = \Yii::$app->db->beginTransaction();

			$this->key = $newkey;

			if($this->update(runValidation: true)) {
				$transaction->commit();
			} else {
				$transaction->rollBack();
				$message = 'Metadatum Key could not be updated.';
				if(!is_null($newval)) $message .= ' Metadatum Value was not attempted.';
				throw new Exception(message: $message . PHP_EOL . implode(separator: PHP_EOL, array: $this->getErrorSummary(showAllErrors: true)));
			}
		}

		if(!is_null($newval))
		{
			$transaction = \Yii::$app->db->beginTransaction();

			$this->value = base64_encode(\Yii::$app->getSecurity()->encryptByKey($newval, \Yii::$app->params['encryptionKey']));

			if($this->update(runValidation: true)) {
				$transaction->commit();
			} else {
				$transaction->rollBack();
				$message = 'Metadatum Value could not be updated.';
				if(!is_null($newkey)) $message .= ' Metadatum Key was saved successfully.';
				throw new Exception(message: $message . PHP_EOL . implode(separator: PHP_EOL, array: $this->getErrorSummary(showAllErrors: true)));
			}
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