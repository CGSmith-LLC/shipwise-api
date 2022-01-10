<?php


namespace common\models;

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
 * @property string $platform
 * @property string $type
 * @property int $status
 * @property string $status_message
 * @property string $last_success_run;
 * @property string $created_date;
 * @property string $updated_date;
 */
class Integration extends ActiveRecord
{
    /**
     * Add new status to generateActionList() method below
     */
    const DISABLED = 0; // Disabled for billing
    const PENDING = 1; // Have not connected yet but will attempt
    const VERIFYING = 2; // currently verifying connection
    const INACTIVE = 3; // customer disabled
    const ERROR = 4; // could not connect
    const ACTIVE = 5; // successfully connected and querying orders

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
            [['name', 'platform', 'type'], 'string', 'max' => 64],
            [['status_message'], 'string', 'max' => 100],
            [['last_success_run'], 'safe'],
            [['customer_id', 'status'], 'integer'],
        ];
    }

    /** @inheritDoc */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'customer_id' => 'Customer',
            'platform' => 'Integration Platform',
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

    /**
     * Relation to meta datums
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeta()
    {
        return $this->hasMany(IntegrationMeta::class, ['integration_id' => 'id']);
    }


    public function getAdapter()
    {
        $adaptername = '\\common\\adapters\\ecommerce\\' . $this->platform . 'Adapter';
        return new $adaptername();
    }

    /**
     * @return BaseEcommerceService
     */
    public function getService(): BaseEcommerceService
    {
        $serviceName = '\\common\\services\\ecommerce\\' . ucfirst($this->platform) . 'Service';

        /** @var BaseEcommerceService $service */
        $service = new $serviceName();
        $service->integration = $this;
        $service->applyMeta($this->getMeta()->all());

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

    /**
     * Generate an action list - typically used by an administrator or customer service to change status
     *
     * @return array[]
     */
    public function generateActionList()
    {
        return [
            ['label' => 'Pending', 'url' => ['integration/status', 'id' => $this->id, 'status' => self::PENDING]],
            ['label' => 'Disabled', 'url' => ['integration/status', 'id' => $this->id, 'status' => self::DISABLED]],
            ['label' => 'Verifying', 'url' => ['integration/status', 'id' => $this->id, 'status' => self::VERIFYING]],
            ['label' => 'Inactive', 'url' => ['integration/status', 'id' => $this->id, 'status' => self::INACTIVE]],
            ['label' => 'Error', 'url' => ['integration/status', 'id' => $this->id, 'status' => self::ERROR]],
            ['label' => 'Active', 'url' => ['integration/status', 'id' => $this->id, 'status' => self::ACTIVE]],
        ];
    }
}