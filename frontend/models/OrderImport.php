<?php

namespace frontend\models;

use Yii;
use yii\base\{
    Exception, Model
};
use yii\helpers\Json;
use yii\web\UploadedFile;
use common\models\{Country, Order, Item, shipping\Carrier, shipping\Service, shipping\Shipment, State, Status};

/**
 * Class OrderImport
 *
 * OrderImport is the model behind the `Import Orders` upload form.
 *
 * @package frontend\models
 *
 * @property int          $customer
 * @property UploadedFile $file
 */
class OrderImport extends Model
{
    public $customer;

    // Fields to be exported
    public static $csvFields = [
        'order_no',           // = orders.customer_reference
        'item_sku',           // = items.sku
        'item_quantity',      // = items.quantity
        'item_name',          // = items.name
        'shipto_name',        // = address.name
        'shipto_address',     // = address.address1
        'shipto_address2',    // = address.address2
        'shipto_city',        // = address.city
        'shipto_state',       // = address.state_id
        'shipto_zip',         // = address.zip
        'shipto_country',     // = address.country
        'shipto_phone',       // = address.phone
        'shipto_email',       // = address.email
        'carrier_service',    // = orders.service_id
        'requested_ship_date',// = orders.requested_ship_date
    ];

    /**
     * Sample data for CSV template file
     *
     * @return array
     */
    public static function getSampleData()
    {
        $now = (new \DateTime('now'));

        return [
            [
                'order_no'            => '100006286-AMBIENT',
                'item_sku'            => 'FF55',
                'item_quantity'       => '2',
                'item_name'           => 'test',
                'shipto_name'         => 'Andrew DiFeo',
                'shipto_address'      => '176 N Wells St',
                'shipto_address2'     => 'suite 100',
                'shipto_city'         => 'Chicago',
                'shipto_state'        => 'IL',
                'shipto_zip'          => '60606',
                'shipto_country'      => 'US',
                'shipto_phone'        => '3123327272',
                'shipto_email'        => 'example@example.com',
                'carrier_service'     => 'UPSGround',
                'requested_ship_date' => $now->format('Y-m-d'),
            ],
            [
                'order_no'            => '100006286-AMBIENT',
                'item_sku'            => 'FF57',
                'item_quantity'       => '1',
                'item_name'           => 'test',
                'shipto_name'         => 'Andrew DiFeo',
                'shipto_address'      => '176 N Wells St',
                'shipto_address2'     => 'suite 100',
                'shipto_city'         => 'Chicago',
                'shipto_state'        => 'IL',
                'shipto_zip'          => '60606',
                'shipto_country'      => 'US',
                'shipto_phone'        => '3123327272',
                'shipto_email'        => 'example@example.com',
                'carrier_service'     => 'UPSGround',
                'requested_ship_date' => $now->format('Y-m-d'),
            ],
        ];
    }

    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file', 'customer'], 'required'],
            [
                ['file'],
                'file',
                'extensions'               => 'csv',
                'checkExtensionByMimeType' => false,
            ],
            [
                'customer',
                'in',
                'range' => array_keys(
                    Yii::$app->user->identity->isAdmin
                        ? Customer::getList()
                        : Yii::$app->user->identity->getCustomerList()
                ),
            ],
        ];
    }

    /**
     * Process the import from CSV file into DB
     *
     * This function will read the CSV file line by line. Create a new order/item and address model.
     * If an order has multiple items, they will be added to the already processed (in the current CSV file) order.
     *
     * @return bool
     */
    public function import()
    {
        ini_set('memory_limit', '1024M');

        $this->file = UploadedFile::getInstance($this, 'file');

        if (!($this->file && $this->validate())) {
            return false;
        }

        $countries       = Country::getList(); // abbr => name
        $services        = array_flip(Service::getShipwiseCodes()); // shipwise_code => id
        $processedOrders = []; // order_no => order id (newly inserted pk)

        // Begin DB transaction
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Open file for reading
            $file = fopen($this->file->tempName, 'r');

            /** @var bool model validation flag */
            $modelValidated = true;

            // Iterate CSV file
            $idx = 0;
            while (($line = fgetcsv($file)) !== false) {
                // Headers row
                if ($idx == 0) {
                    // Validate number of columns
                    if (count($line) !== count(self::$csvFields)) {
                        throw new Exception(
                            'Incorrect number of columns in file. CSV file must have ' .
                            count(self::$csvFields) . ' columns. Please download and use the correct template.'
                        );
                    }
                    $idx++;
                    continue; // skip headers row
                }

                $data = array_combine(self::$csvFields, $line);

                // \yii\helpers\VarDumper::dump($data, 10, true); exit;

                if (empty($data['order_no'])) {
                    // If the first required column "order_no" is empty then we consider that there is no data in this row.
                    continue;
                }
                $orderNo = trim($data['order_no']);

                /**
                 * Validations
                 */
                $country = trim($data['shipto_country']);
                if (!isset($countries[$country])) {
                    $msg = 'Row# ' . ($idx + 1) . " (Order# {$orderNo})";
                    $this->addError('orders' . ($idx + 1) . 'country', "$msg Invalid country.");
                    $modelValidated = false;
                    $idx++;
                    continue;
                }
                $state   = Shipment::recognizeState($country, trim($data['shipto_state']));
                $stateId = State::findByAbbrOrName($country, $state, $state)->id ?? null;
                if (is_null($stateId)) {
                    $msg = 'Row# ' . ($idx + 1) . " (Order# {$orderNo})";
                    $this->addError('orders' . ($idx + 1) . 'state', "$msg Invalid state/province.");
                    $modelValidated = false;
                    $idx++;
                    continue;
                }
                $service = trim($data['carrier_service']);
                if (!isset($services[$service])) {
                    $msg = 'Row# ' . ($idx + 1) . " (Order# {$orderNo})";
                    $this->addError('orders' . ($idx + 1) . 'service', "$msg Invalid shipping service code.");
                    $modelValidated = false;
                    $idx++;
                    continue;
                }

                if (!isset($processedOrders[$orderNo])) { // CASE: New order

                    // Address
                    $address           = new Address();
                    $address->name     = trim($data['shipto_name']);
                    $address->address1 = trim($data['shipto_address']);
                    $address->address2 = trim($data['shipto_address2']) ?? null;
                    $address->city     = trim($data['shipto_city']);
                    $address->country  = $country;
                    $address->state_id = $stateId;
                    $address->zip      = trim($data['shipto_zip']);
                    $address->phone    = trim($data['shipto_phone']);
                    $address->email    = trim($data['shipto_email']) ?? null;
                    // Validate and save Address object
                    if (!$address->save()) {
                        foreach ($address->getErrors() as $attr => $error) {
                            $msg = 'Row# ' . ($idx + 1) . " (Order# {$orderNo})";
                            $msg = "$msg  " . Json::encode($error);
                            $this->addError('order.address' . ($idx + 1) . $attr, $msg);
                        }
                        $modelValidated = false;
                        $idx++;
                        continue;
                    }

                    // Order
                    $order                      = new Order();
                    $order->customer_id         = $this->customer;
                    $order->origin              = Yii::$app->name . ' CSV import';
                    $order->customer_reference  = $orderNo;
                    $order->address_id          = $address->id;
                    $order->status_id           = Status::OPEN;
                    $order->requested_ship_date = date("Y-m-d", strtotime(trim($data['requested_ship_date'])));
                    $order->service_id          = $services[$service];
                    $order->carrier_id          = Carrier::findByServiceCode($service)->id ?? null;

                    // Validate and save Order object
                    if ($order->save()) {
                        $processedOrders[$orderNo] = $order->id;
                        $orderId                   = $order->id;
                    } else {
                        foreach ($order->getErrors() as $attr => $error) {
                            $msg = 'Row# ' . ($idx + 1) . " (Order# {$orderNo})";
                            $msg = "$msg  " . Json::encode($error);
                            $this->addError('orders' . ($idx + 1) . $attr, $msg);
                        }
                        $modelValidated = false;
                        $idx++;
                        continue;
                    }
                } else {
                    // CASE: Current item will be appended to the previously processed (in this CSV file) order.
                    $orderId = $processedOrders[$orderNo];
                }

                // Item
                $item           = new Item();
                $item->order_id = $orderId;
                $item->sku      = trim($data['item_sku']);
                $item->quantity = trim($data['item_quantity']);
                $item->name     = trim($data['item_name']) ?? null;
                // Validate and save Item object
                if (!$item->save()) {
                    foreach ($item->getErrors() as $attr => $error) {
                        $msg = 'Row# ' . ($idx + 1) . " (Order# {$orderNo})";
                        $msg = "$msg  " . Json::encode($error);
                        $this->addError('order.items' . ($idx + 1) . $attr, $msg);
                    }
                    $modelValidated = false;
                    $idx++;
                    continue;
                }

                $idx++;

                unset($data, $address, $order, $item, $orderNo, $orderId, $country, $stateId, $service);
            } // end while

            if (!$modelValidated) {
                throw new Exception(
                    'Validation errors found. Please edit and re-upload your CSV file.'
                );
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->addError('file', 'Import failed. ' . $e->getMessage());

            return false;
        } finally {
            if (isset($file)) {
                fclose($file);
            }
        }

        return true;
    }
}
