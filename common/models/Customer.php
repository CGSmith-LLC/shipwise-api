<?php

namespace common\models;

use common\models\base\BaseCustomer;
use frontend\models\User;
use yii\helpers\ArrayHelper;
/**
 * Class Customer
 *
 * @property string $country Country two-chars ISO code
 *
 * @package common\models
 */
class Customer extends BaseCustomer
{

    public $country = 'US';

    /**
     * Returns list of Countries as array [abbreviation=>name]
     *
     * @param string $keyField Field name to use as key
     * @param string $valueField Field name to use as value
     *
     * @return array
     */
    public static function getList($keyField = 'id', $valueField = 'name')
    {
        $data = self::find()->orderBy([$valueField => SORT_ASC])->all();

        return ArrayHelper::map($data, $keyField, $valueField);
    }

	public function getBillingEmail()
	{
		$billingEmail = \Yii::$app->customerSettings->get('billing_email', $this->id);
		if (!empty($billingEmail)) {
			$customerEmails = explode(',', $billingEmail);
		} else {
			$customers = User::find()->where(['customer_id' => $this->id])->all();
			$customerEmails = ArrayHelper::map($customers, 'email', 'email');
		}

		return $customerEmails;
	}
}
