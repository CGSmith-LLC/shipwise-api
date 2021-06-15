<?php

namespace frontend\models\forms;

use Yii;
use yii\base\Model;

/**
 * Class BulkEditForm
 *
 * This model is for modifying multiple orders
 *
 * @package frontend\models\forms
 */
class BulkEditForm extends Model
{

    /** @var array of Order items */
    protected $_orders = [];

    /** @var string User input that needs to be parsed into order identifiers*/

    public $customer;

    /** @var string Action to be performed */
    public $action;

    /** @var string A delineated list of order reference numbers */
    public $order_ids;

    public $delimiter = 'newlines';

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['customer', 'action', 'order_ids', 'delimiter'], 'required'], // @todo validate as text field
        ];
    }


}
