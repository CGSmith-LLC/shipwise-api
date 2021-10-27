<?php


namespace common\models;

use common\adapters\ecommerce\BaseECommerceAdapter;
use common\services\ecommerce\BaseEcommerceService;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "integration".
 *
 * @property string $id
 * @property string $name
 * @property int $customer_id
 * @property string $ecommerce
 * @property string $fulfillment
 * @property int $status
 */
class Integration extends ActiveRecord
{
    const DISABLED = 0; // Disabled for billing
    const PENDING = 1;
    const VERIFYING = 2;
    const INACTIVE = 3;
    const ERROR = 4;
    const ACTIVE = 5;

    /** @inheritDoc */
    public static function tableName(): string
    {
        return "integration";
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_date', 'updated_date'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_date'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /** @inheritDoc */
    public function rules(): array
    {
        return [
            [['name', 'customer_id', 'ecommerce', 'status'], 'required'],
            [['name', 'ecommerce', 'fulfillment'], 'string', 'max' => 64],
            [['customer_id', 'status'], 'integer'],
        ];
    }

    /** @inheritDoc */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'customer_id' => 'Customer ID',
            'ecommerce' => 'Ecommerce Platform',
        ];
    }

    /**
     * Relation for customer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\frontend\models\Customer::class, ['id' => 'customer_id']);
    }

    public function getAdapter($json, $customer_id): BaseECommerceAdapter
    {
        $adaptername = '\\common\\adapters\\ecommerce\\' . ucfirst($this->ecommerce) . 'Adapter';
        return new $adaptername(json: $json, customer_id: $customer_id);
    }

    /**
     * @return BaseEcommerceService
     */
    public function getService(): BaseEcommerceService
    {
        $serviceName = '\\common\\services\\ecommerce\\' . ucfirst($this->ecommerce) . 'Service';

        /** @var BaseEcommerceService $service */
        $service = new $serviceName();
        $service->applyMeta(metadata: IntegrationMeta::find()->where(['integration_id' => $this->id])->all());

        return $service;
    }

    /**
     * Status label
     *
     * @param bool $html Whether to return in html format
     *
     * @return string
     */
    public function getStatusLabel($html = true)
    {
        $status = '';
        switch ($this->status) {
            case self::PENDING:
                $status = $html ? '<p class="label label-primary">Pending</p>' : 'Pending';
                break;
            case self::ACTIVE:
                $status = $html ? '<p class="label label-success">Active</p>' : 'Active';
                break;
            case self::ERROR:
                $status = $html ? '<p class="label label-danger">Error</p>' : 'Error';
                break;
            case self::VERIFYING:
                $status = $html ? '<p class="label label-warning">Verifying</p>' : 'Verifying';
                break;
            case self::DISABLED:
                $status = $html ? '<p class="label label-default">Disabled</p>' : 'Disabled';
                break;
            case self::INACTIVE:
                $status = $html ? '<p class="label label-info">Inactive</p>' : 'Inactive';
        }

        return $status;
    }
}