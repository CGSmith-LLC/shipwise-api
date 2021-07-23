<?php


namespace common\adapters;


use common\models\Address;
use common\models\Item;
use common\models\Order;
use common\models\shipping\Carrier;
use common\models\State;
use common\models\Status;
use yii\base\BaseObject;
use yii\db\Exception;

/**
 * Class ECommerceAdapter
 * @package common\adapters
 *
 * @var string $referenceNumber ;
 * @var int $UUID ;
 * @var int $status ;
 * @var string $origin ;
 * @var string $notes ;
 * @var string $orderNotes ;
 * @var int $customerID ;
 *
 * @var string $shipToEmail ;
 * @var string $shipToName ;
 * @var string $shipToAddress1 ;
 * @var string $shipToAddress2 ;
 * @var string $shipToCompany ;
 * @var string $shipToCity ;
 * @var string $shipToState ;
 * @var string $shipToZip ;
 * @var string $shipToPhone ;
 * @var string $shipToCountry ;
 *
 * @var string $shippingService ;
 *
 * @var Item[] $items ;
 */
abstract class ECommerceAdapter extends BaseObject
{

    // TODO: Add types
    protected ?string $referenceNumber;
    protected int $UUID;
    protected int $status;
    protected string $origin;
    protected ?string $notes;
    protected ?string $orderNotes;
    protected int $customerID;

    protected string $shipToEmail;
    protected string $shipToName;
    protected string $shipToAddress1;
    protected ?string $shipToAddress2;
    protected ?string $shipToCompany;
    protected string $shipToCity;
    protected string $shipToState;
    protected string $shipToZip;
    protected ?string $shipToPhone;
    protected string $shipToCountry;

    protected int $shippingService;

    protected array $items;

    public function __construct($json, $customer_id)
    {
        parent::__construct();

        echo "building order..." . PHP_EOL;
        $this->status = Status::OPEN;
        $this->customerID = $customer_id;

        $this->buildGeneral($json);
        $this->buildAddress($json);
        $this->buildShipping($json);
        $this->buildItems($json);
        echo "built order" . PHP_EOL;
    }

    public function parse(): Order
    {
        echo "parsing order..." . PHP_EOL;
        $shipwiseOrder = new Order();
        $shipwiseOrder = self::setGeneralInfo($shipwiseOrder);
        $shipwiseOrder = self::setAddressInfo($shipwiseOrder);
        $shipwiseOrder = self::setShippingInfo($shipwiseOrder);
        echo "parsed order" . PHP_EOL . PHP_EOL;
        return $shipwiseOrder;
    }

    /**
     * @throws Exception
     */
    public function parseItems(): bool
    {
        echo "parsing items..." . PHP_EOL;
        $transaction = \Yii::$app->db->beginTransaction();

        $id = Order::findOne([
            'status_id' => Status::OPEN,
            'origin' => $this->origin,
            'service_id' => $this->shippingService,
            'uuid' => $this->UUID,
            'order_reference' => $this->referenceNumber,
        ])->id;

        foreach ($this->items as $item) {
            echo "\tparsing item...\t";
            if (!(new Item($item))->save(true)) {
                $transaction->rollBack();
                return false;
            }
            echo "parsed item" . PHP_EOL;
        }

        $transaction->commit();
        echo "parsed items" . PHP_EOL . PHP_EOL;
        return true;
    }

    /**
     * @param Order $order
     * @return Order
     */
    private function setGeneralInfo(Order $order): Order
    {
        echo "\tparsing general...\t";
        $order->order_reference = $this->referenceNumber;
        $order->uuid = $this->UUID;
        $order->status_id = Status::OPEN;
        $order->origin = $this->origin;
        $order->notes = $this->notes;
        echo 'parsed general' . PHP_EOL;
        return $order;
    }

    /**
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    private function setAddressInfo(Order $order): Order
    {
        echo "\tparsing address..." . PHP_EOL;

        $values = [
            'email' => $this->shipToEmail,
            'name' => $this->shipToName,
            'address1' => $this->shipToAddress1,
            'company' => $this->shipToCompany,
            'city' => $this->shipToCity,
            'state_id' => State::findByAbbrOrName($this->shipToCountry, null, $this->shipToState)->id,
            'zip' => $this->shipToZip,
            'phone' => $this->shipToPhone,
            'country' => $this->shipToCountry,

        ];

        if (isset($this->shipToAddress2)) {
            $values['address2'] = $this->shipToAddress2;
        }
        if (isset($this->orderNotes) && !empty($this->orderNotes)) {
            $values['notes'] = $this->orderNotes;
        }

        echo "\t\tcreating new address...\t";
        $address = new Address();
        $address->attributes = $values;

        $transaction = \Yii::$app->db->beginTransaction();
        if (!$address->save(true)) {
            $transaction->rollBack();
            throw new Exception('New address entry could not be created.');
        }
        $transaction->commit();
        echo "new address created" . PHP_EOL;

        $order->address_id = $address->id;
        echo "\tparsed address" . PHP_EOL;
        //$order->ship_from_state_id = State::findByAbbrOrName()->id;

        return $order;
    }

    /**
     * @param Order $order
     * @return Order
     */
    private function setShippingInfo(Order $order): Order
    {
        echo "\tparsing shipping...\t";
        $order->carrier_id = Carrier::findOne(["name" => "FedEx"])->id; //TODO: Add ability to handle different carriers

        $order->service_id = $this->shippingService;
        echo 'parsed shipping' . PHP_EOL;
        return $order;
    }

    protected abstract function buildGeneral($json);

    protected abstract function buildAddress($json);

    protected abstract function buildShipping($json);

    protected abstract function buildItems($json);
}