<?php

namespace common\models;

use common\models\query\WebhookQuery;
use Da\User\Model\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "webhooks".
 *
 * @property int      $id
 * @property string   $name
 * @property string   $endpoint
 * @property int      $authentication_type
 * @property string   $signing_secret
 * @property string   $user
 * @property string   $pass
 * @property int      $customer_id
 * @property int      $active
 * @property int      $created_at
 * @property int      $updated_at
 *
 * @property Customer $customer
 */
class Webhook extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const NO_AUTH = 0;
    const BASIC_AUTH = 1;
    const HEADER_AUTH = 2;

    public array $authenticationOptions = [
        self::NO_AUTH => 'None',
        self::BASIC_AUTH => 'Basic Auth',
        self::HEADER_AUTH => 'Header Auth',
    ];

    public array $activeOptions = [
        self::STATUS_ACTIVE => 'Enabled',
        self::STATUS_INACTIVE => 'Disabled',
    ];

    public $triggers;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'webhook';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['triggers', 'endpoint', 'name', 'customer_id', 'user_id', 'active', 'signing_secret'], 'required'],
            [['authentication_type', 'user_id', 'customer_id', 'active', 'created_at', 'updated_at'], 'integer'],
            [['name', 'user', 'pass', 'signing_secret'], 'string', 'max' => 255],
            [['endpoint'], 'url', 'validSchemes' => ['https']],
            [
                ['customer_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Customer::class,
                'targetAttribute' => ['customer_id' => 'id']
            ],
            [['customer_id'], 'validateUserRelatesToCustomer'],
        ];
    }

    /**
     * Ensure that the user saving the form has rights to save against the customer_id
     * I added this so someone couldn't receive webhooks from another customer by brute force
     *
     * @param $attribute
     * @param $params
     * @param $validator
     * @return void
     */
    public function validateUserRelatesToCustomer($attribute, $params, $validator)
    {
        if (!Yii::$app->user->identity->isAdmin &&
            !in_array($this->$attribute, array_values(Yii::$app->user->identity->customerIds)) ) {
            $this->addError($attribute, 'There was an error with associating with a customer');
        }
    }

    /**
     * Make sure that the user id is set on the webhook model - provided for notifications if webhook errors
     *
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->signing_secret = $this->generateSigningSecret();
        }
        $this->user_id = Yii::$app->user->id;
        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'endpoint' => 'Endpoint',
            'authentication_type' => 'Authentication Type',
            'user' => 'Basic Auth User or Header Key',
            'pass' => 'Basic Auth Pass or Header Value',
            'customer_id' => 'Customer ID',
            'user_id' => 'User ID',
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function afterFind()
    {
        $this->triggers = ArrayHelper::map($this->getWebhookTrigger()->all(), 'id', 'status_id');
        parent::afterFind();
    }

    /**
     * Creates new relations after deleting old ones
     * @return void
     */
    public function createNewRelations()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $triggers = WebhookTrigger::find()->where(['webhook_id' => $this->id])->all();
            foreach ($triggers as $trigger) {
                $trigger->delete();
            }

            foreach ($this->triggers as $newTriggerStatus) {
                $trigger = new WebhookTrigger();
                $trigger->webhook_id = $this->id;
                $trigger->status_id = (int) $newTriggerStatus;
                $trigger->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::debug($e);
            $transaction->rollBack();
        }
    }

    /**
     * @inheritdoc
     * @return WebhookQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WebhookQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getWebhookTrigger()
    {
        return $this->hasMany(WebhookTrigger::class, ['webhook_id' => 'id']);
    }

    public function getWebhookLog()
    {
        return $this->hasMany(WebhookLog::class, ['webhook_id' => 'id']);
    }

    public function getLabelFor($attribute)
    {
        $infoColor = 'info';
        $name = null;
        $template = '<span class="label label-%s">%s</span>';
        if ($attribute == 'authentication_type') {
            return match ($this->$attribute) {
                self::HEADER_AUTH, self::BASIC_AUTH => sprintf(
                    $template,
                    $infoColor,
                    $this->authenticationOptions[$this->$attribute]
                ),
                default => '',
            };
        }

        if ($attribute == 'active') {
            $infoColor = ($this->active === self::STATUS_ACTIVE) ? 'success' : 'default';
            $name = $this->activeOptions[$this->active];
        }

        if (isset($this->$attribute)) {
            return sprintf($template, $infoColor, (isset($name)) ? $name : $this->$attribute);
        }

        // return empty string if nothing to match
        return '';
    }


    public function getMasked($attribute)
    {
        return substr($this->$attribute, 0, strlen($this->$attribute) / 5) . '*************** ';
    }

    public function regenerateSigningSecret()
    {
        $this->signing_secret = $this->generateSigningSecret();
        return $this->save();
    }

    public function generateSigningSecret()
    {
        return Yii::$app->getSecurity()->generateRandomString();
    }
}
