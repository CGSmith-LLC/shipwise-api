<?php

namespace api\modules\v1\models;

use api\modules\v1\models\core\CarrierEx;
use api\modules\v1\models\core\ServiceEx;
use Aws\ResultInterface;
use common\models\Address;
use common\models\Country;
use common\models\Item;
use common\models\Order;
use common\models\shipping\Shipment;
use common\models\State;
use common\models\Status;
use League\Csv\Reader;
use League\Csv\Statement;
use yii\base\Exception;
use yii\helpers\Json;
use yii\base\Model;

/**
 * Class CsvBox
 * @package api\modules\v1\models
 */
class CsvBox extends Model
{
    public int $sheet_id;
    public string $sheet_name;
    public int $import_id;
    public int $row_count;
    public int $row_success;
    public int $row_fail;
    public string $import_status;
    public $import_starttime;
    public $import_endtime;
    public string $raw_file;
    public array $custom_fields;
    public string $original_filename;
    public array $column_mappings;
    public string $file_path = '';
    public ResultInterface $file_stream;
    public string $user_id = '';
    public string $destination_type;
    public string $customer_id = '';

    /** @var array Orders uploaded or errored */
    public array $orders = [];

    public function rules(): array
    {
        return [
            [['sheet_id', 'import_id', 'row_count', 'row_success', 'row_fail', 'file_path'], 'required'],
            [['sheet_id', 'import_id', 'row_count', 'row_success'], 'integer'],
            [['sheet_name', 'import_status', 'raw_file', 'original_filename', 'file_path'], 'string'],
            [
                ['custom_fields', 'column_mappings'],
                function ($attribute, $params) {
                    if (!is_array($this->$attribute)) {
                        $this->addError($attribute, $attribute . ' is not an array');
                    }
                }
            ],
            [['row_fail'], 'integer', 'min' => 0, 'max' => 0],
            [['import_starttime', 'import_endtime'], 'integer'],
        ];
    }

    public function init(): void
    {
        parent::init();
        $this->user_id = $this->custom_fields['user_id'];
        $this->customer_id = $this->custom_fields['customer_id'];
    }

    public function import(): bool
    {
        $countries = Country::getList(); // abbr => name
        $processedOrders = []; // order_no => order id (newly inserted pk)

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $modelValidated = true; // model validation flag

            $csv = Reader::createFromString($this->file_stream->get('Body')->getContents());
            $csv->setHeaderOffset(0); //set the CSV header offset

            $stmt = Statement::create();
            $records = $stmt->process($csv);
            foreach ($records as $key => $record) {
                $orderNo = trim($record['Order Reference']);

                // if order exists in database, abort and show message to user
                if (!isset($processedOrders[$orderNo])
                    && (Order::find()->byCustomerReference($orderNo)->forCustomer($this->customer_id)->one())) {
                    $this->addError('orders' . $key . 'order', "Order #$orderNo already exists");
                    $modelValidated = false;
                    continue;
                }

                /**
                 * Validations
                 */
                // Country check
                $country = trim($record['Country']);
                if (!isset($countries[$country])) {
                    $msg = 'Row# ' . $key . " (Order# {$orderNo})";
                    $this->addError('orders' . $key . 'country', "$msg Invalid country.");
                    $modelValidated = false;
                }

                // State check
                $state = Shipment::recognizeState($country, trim($record['State']));
                $stateId = State::findByAbbrOrName($country, $state, $state)->id ?? null;
                if (is_null($stateId)) {
                    $msg = 'Row# ' . $key . " (Order# {$orderNo})";
                    $this->addError('orders' . $key . 'state', "$msg Invalid state/province.");
                    $modelValidated = false;
                }

                if (!isset($processedOrders[$orderNo])) { // CASE: New order - create Address and Order objects.
                    // Address
                    $address = new Address();
                    $address->name = trim($record['Name']);
                    $address->company = trim($record['Company']) ?? null;
                    $address->address1 = trim($record['Address']);
                    $address->address2 = trim($record['Address 2']) ?? null;
                    $address->city = trim($record['City']);
                    $address->country = $country;
                    $address->state_id = $stateId;
                    $address->zip = trim($record['Zip']);
                    $address->phone = trim($record['Phone']) ?: '5555555555';
                    $address->email = trim($record['Email']) ?? null;
                    $address->notes = trim($record['Notes']) ?? null;
                    // Validate and save Address object
                    if (!$address->save()) {
                        foreach ($address->getErrors() as $attr => $error) {
                            $msg = 'Row# ' . $key . " (Order# {$orderNo})";
                            $msg = "$msg  " . Json::encode($error);
                            $this->addError('order.address' . $key . $attr, $msg);
                        }
                        $modelValidated = false;
                        continue;
                    }

                    // Order
                    $carrier = CarrierEx::find()->where(['name' => $record['Carrier']])->one();
                    $service = ServiceEx::find()->where(['name' => $record['Service']])->one();

                    $requestedShipDate = date("Y-m-d", strtotime(trim($record['Requested Ship Date'])));
                    $mustArriveByDate = ($record['Must Arrive By Date']) ? date("Y-m-d", strtotime(trim($record['Must Arrive By Date']))) : null;

                    $order = new Order();
                    $order->customer_id = $this->customer_id;
                    $order->origin = !empty(trim($record['Origin'])) ? trim($record['Origin']) : \Yii::$app->name . ' CSV import';
                    $order->customer_reference = $orderNo;
                    $order->address_id = $address->id;
                    $order->status_id = Status::OPEN;
                    $order->requested_ship_date = $requestedShipDate;
                    $order->must_arrive_by_date = $mustArriveByDate;
                    $order->service_id = $service->id;
                    $order->carrier_id = $carrier->id;

                    // Validate and save Order object
                    if ($order->save()) {
                        $processedOrders[$orderNo] = $order->id;
                        \Yii::debug($record);
                    } else {
                        \Yii::debug($order->getErrorSummary(true));
                        foreach ($order->getErrors() as $attr => $error) {
                            $msg = 'Row# ' . $key . " (Order# {$orderNo})";
                            $msg = "$msg  " . Json::encode($error);
                            $this->addError('orders' . $key . $attr, $msg);
                        }
                        $modelValidated = false;
                        continue;
                    }
                }

                // Item
                $item = new Item();
                $item->order_id = $processedOrders[$orderNo];
                $item->sku = trim($record['Item Sku']);
                $item->quantity = trim($record['Item Quantity']);
                $item->name = utf8_decode(trim($record['Item Name'])) ?? null;
                // Validate and save Item object
                if (!$item->save()) {
                    foreach ($item->getErrors() as $attr => $error) {
                        $msg = 'Row# ' . $key . " (Order# {$orderNo})";
                        $msg = "$msg  " . Json::encode($error);
                        $this->addError('order.items' .$key . $attr, $msg);
                    }
                    $modelValidated = false;
                    continue;
                }

                unset($data, $address, $order, $item, $orderNo, $country, $stateId);

                if ($modelValidated === false) {
                    throw new Exception('Validation errors found. Please edit and re-upload your CSV file.');
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('file', 'Import failed. ' . $e->getMessage());

            return false;
        }
    }

    public function getS3FilePath(): string
    {
        return
            $this->file_path .
            '/' .
            $this->import_id .
            '_' .
            $this->user_id .
            '.csv';
    }
}