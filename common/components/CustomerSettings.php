<?php

namespace common\components;

use yii\base\Component;
use Yii;
use common\models\CustomerMeta;

/**
 * Class CustomerSettings
 *
 * Customer settings.
 *
 * Settings are retrieved from database table `customers_meta`,
 * if no setting exists in customer meta table, then it is retrieved from Yii::$app->params['globalCustomerSettings'].
 *
 */
class CustomerSettings extends Component
{

    /**
     * Get setting
     *
     * If setting for given customer exists return it,
     * if not, return from from Yii::$app->params['globalCustomerSettings'] if available.
     *
     * Usage Example:
     *
     * ```php
     * // get setting by key name for customer with ID 1
     * Yii::$app->customerSettings->get('fedex_api_account', '1');
     * ```
     *
     * @param string   $key        Settings key
     * @param int|null $customerId Customer ID
     *
     * @return null|string
     */
    public static function get($key, $customerId = null)
    {
        $customerMeta = CustomerMeta::findOne(['customer_id' => $customerId, 'key' => $key]);

        return ($customerMeta->value ?? Yii::$app->params['globalCustomerSettings'][$key] ?? null);
    }
}