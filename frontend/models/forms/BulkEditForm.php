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
    public $orders_input;
    public $customer;

    /** @var string Action to be performed */
    public $action;

    /** @var array Variables to pass along to the action to be performed */
    public $action_variables = [];

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['customer', 'action', 'action_variables', 'orders_input'], 'required'], // @todo validate as text field
        ];
    }


}
