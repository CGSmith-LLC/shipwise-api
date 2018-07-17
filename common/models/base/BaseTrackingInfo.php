<?php

namespace common\models\base;

/**
 * This is the model class for table "tracking_info".
 *
 * @property int    $id
 * @property int    $carrier_id
 * @property int    $service_id
 * @property string $tracking
 * @property string $created_date
 */
class BaseTrackingInfo extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'tracking_info';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['carrier_id', 'service_id', 'tracking'], 'required'],
			[['carrier_id', 'service_id'], 'integer'],
			[['created_date'], 'safe'],
			[['tracking'], 'string', 'max' => 100],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'           => 'ID',
			'carrier_id'   => 'Carrier ID',
			'service_id'   => 'Service ID',
			'tracking'     => 'Tracking',
			'created_date' => 'Created Date',
		];
	}
}