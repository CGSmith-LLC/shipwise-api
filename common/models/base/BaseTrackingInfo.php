<?php

namespace common\models\base;

/**
 * This is the model class for table "tracking_info".
 *
 * @property int    $id
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
			[['service_id', 'tracking'], 'required'],
			[['service_id'], 'integer'],
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
			'service_id'   => 'Service ID',
			'tracking'     => 'Tracking',
			'created_date' => 'Created Date',
		];
	}
}