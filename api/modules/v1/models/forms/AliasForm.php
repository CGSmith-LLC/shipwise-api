<?php

namespace api\modules\v1\models\forms;

use api\modules\v1\models\alias\AliasChildrenEx;
use api\modules\v1\models\alias\AliasEx;

/**
 * @SWG\Definition(
 *     definition = "AliasForm",
 *     required   = { "name", "sku", "children" },
 *     @SWG\Property(
 *            property = "name",
 *            type = "string",
 *            description = "Alias name",
 *            minLength = 1,
 *            maxLength = 128
 *        ),
 *     @SWG\Property(
 *            property = "sku",
 *            type = "string",
 *            description = "SKU to look for",
 *            minLength = 1,
 *            maxLength = 64
 *        ),
 *     @SWG\Property(
 *          property = "children",
 *          type = "array",
 *          @SWG\Items( ref = "#/definitions/AliasChildren" )
 *     ),
 * )
 */

/**
 * Class AliasForm
 *
 * @package api\modules\v1\models\forms
 */
class AliasForm extends AliasEx
{

    public string $name;
    public int $customer_id;
    public string $sku;
    public array $children;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'name', 'sku', 'children'], 'required'],
            [['customer_id'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['sku'], 'string', 'max' => 64],
            [['children'], 'checkIsArray'],
        ];
    }

    public function checkIsArray()
    {
        if (!is_array($this->children) || count($this->children) == 0) {
            $this->addError('children', 'Children must contain an array of aliases');
        }
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $aliasParent = new AliasEx([
                'customer_id' => $this->customer_id,
                'name' => $this->name,
                'sku' => $this->sku,
                'active' => 1,
            ]);
            $aliasParent->save();

            // add children
            foreach ($this->children as $child) {
                $aliasChild = new AliasChildrenEx([
                        'alias_id' => $aliasParent->id,
                        'sku' => $child['sku'],
                        'name' => $child['name'],
                        'quantity' => $child['quantity']
                ]);
                $aliasChild->validate();
                $aliasChild->save();
            }
        } catch (\Exception $e) {
            $this->addError('children', $e->getMessage());
            $transaction->rollBack();
        }
        $transaction->commit();

        return $aliasParent->id;
    }

}