<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_items".
 *
 * @property int $id
 * @property int $invoice_id Reference to invoice table
 * @property string $name
 * @property int $amount cents
 */
class InvoiceItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoice_id', 'name', 'amount'], 'required'],
            [['invoice_id', 'amount'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice ID',
            'name' => 'Name',
            'amount' => 'Amount',
        ];
    }

    /**
     * Relation for Invoice
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::class, ['invoice_id'=>'id']);
    }
}
