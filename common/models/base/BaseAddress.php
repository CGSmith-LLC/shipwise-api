<?php

namespace common\models\base;

/**
 * This is the model class for table "addresses".
 *
 * @property int $id
 * @property string $company
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property int $state_id
 * @property string $zip
 * @property string $email
 * @property string $phone
 * @property string $notes
 * @property string $created_date
 * @property string $updated_date
 * @property string $country
 */
class BaseAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'addresses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address1', 'city', 'zip', 'phone'], 'required'],
            [['state_id'], 'integer'],
            [['state_id'], 'default', 'value' => 0],
            [['created_date', 'updated_date'], 'safe'],
            [['company', 'name', 'address1', 'address2', 'city'], 'string', 'max' => 64],
            [['zip'], 'string', 'max' => 16],
            [['phone'], 'string', 'max' => 32],
            [['notes'], 'string', 'max' => 600],
            [['country'], 'string', 'max' => 2],
            [['country'], 'default', 'value' => 'US'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'company' => 'Company',
            'email' => 'Email',
            'address1' => 'Address Line 1',
            'address2' => 'Address Line 2',
            'city' => 'City',
            'state_id' => 'State ID',
            'state.name' => 'State',
            'zip' => 'ZIP',
            'phone' => 'Phone',
            'notes' => 'Notes',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'country' => 'Country',
        ];
    }
}