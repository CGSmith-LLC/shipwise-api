<?php


namespace common\behaviors;


use common\adapters\ecommerce\WooCommerceAdapter;

class AddSku extends \yii\base\Behavior
{

    public $unparsedOrder;

    public $startDate;

    public $active;

    /***
     * Operation (ID: 1)
     * - Name: 'State Check'
     * - Type: *Logic*, operation, iterator
     * - Input: order.state.abbreviation
     * - Logic: *in_array*, ==, !=,
     * - Comparison (parens for array): ('CA', 'WI', 'IL') - Otherwise it would just be WI
     * - Output True: Perform operation 'Set Warehouse to Reno', nothing
     * - Output False: *Nothing*, Perform operation... (chain an operation)
     *
     * Operation (ID: 2)
     * - Name: 'Set Warehouse to Reno'
     * - Type: *Operation*, logic
     * - Input: n-a
     * - Logic: n-a
     * - Comparison: n-a
     * - Output True: order.billing = 12345
     * - Output False: n-a
     *
     *
     * Behavior
     *  - Name: Check Warehouse
     *  - IDs: {1, 2}
     *  - log behaviors: true/false
     *
     *
     * Operation (ID: 3)
     * - Name: 'Shipping to Wisconsin?'
     * - Type: *Logic*, operation, iterator
     * - Input: order.state.abbreviation
     * - Logic: in_array, *==*, !=,
     * - Comparison (parens for array): WI
     * - Output True: Perform Operation ID 4
     * - Output False: *Nothing*, Perform operation... (chain an operation)
     *
     * Operation (ID: 4)
     * - Name: 'Order Date after 11-19-21?'
     * - Type: *Logic*, operation, iterator
     * - Input: order.createdDate
     * - Logic: in_array, ==, !=, * greater than *, less than
     * - Comparison (parens for array): 2021-11-19
     * - Output True: Perform Operation ID 5, nothing
     * - Output False: *Nothing*, Perform operation... (chain an operation)
     *
     * Operation (ID: 5)
     * - Name: 'Order Date before 11-30-21?'
     * - Type: *Logic*, operation, iterator
     * - Input: order.createdDate
     * - Logic: in_array, ==, !=, greater than, *less than*
     * - Comparison (parens for array): 2021-11-30
     * - Output True: Perform Operation ID 6, nothing
     * - Output False: *Nothing*, Perform operation... (chain an operation)
     *
     * Operation (ID: 6)
     * - Name: 'Add Welcome WI Item'
     * - Type: Logic, *operation*, iterator
     * - Input: n-a
     * - Logic: n-a
     * - Comparison (parens for array): n-a
     * - Output True: addItem(1, 'Welcome WI', 'WELCOME-WI-SKU')
     * - Output False: n-a
     *
     * Behavior
     *  - Welcome WI Customers
     *  - IDs: {3, 4, 5, 6}
     */

    public function events()
    {
        return [
            //WooCommerceAdapter::EVENT_AFTER_PARSE => 'afterParse',
        ];
    }

    public function afterParse()
    {
        var_dump('hello there ---------------------- ' . $this->unparsedOrder->number);
        $this->unparsedOrder->number = '6969';
    }
}