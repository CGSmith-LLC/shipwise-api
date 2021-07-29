<?php


namespace common\adapters\ecommerce;


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
abstract class BaseECommerceAdapter extends BaseObject implements ECommerceAdapter
{

    protected ?string $customer_reference;
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

        $this->status = Status::OPEN;
        $this->customerID = $customer_id;

        $this->buildGeneral($json);
        $this->buildAddress($json);
        $this->buildShipping($json);
        $this->buildItems($json);
    }

    public function parse(): Order
    {
        $shipwiseOrder = new Order();
        $shipwiseOrder = self::setGeneralInfo($shipwiseOrder);
        $shipwiseOrder = self::setAddressInfo($shipwiseOrder);
        $shipwiseOrder = self::setShippingInfo($shipwiseOrder);
        return $shipwiseOrder;
    }

    /**
     * @throws Exception
	 * @return Item[]
     */
    public function parseItems($id): array
    {
        $out = [];

        foreach ($this->items as $item) {
            $newitem = new Item($item);
            $newitem->order_id = $id;

            if (!$newitem->validate()) {
                throw new Exception(implode(PHP_EOL, $newitem->getErrorSummary(true)));
            } else {
                $out[] = $newitem;
            }
        }

        return $out;
    }

    /**
     * @param Order $order
     * @return Order
     */
    private function setGeneralInfo(Order $order): Order
    {
        $order->customer_reference = $this->customer_reference;
        $order->customer_id = $this->customerID;
        $order->uuid = $this->UUID;
        $order->status_id = Status::OPEN;
        $order->origin = $this->origin;
        $order->notes = $this->notes;
        return $order;
    }

    /**
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    private function setAddressInfo(Order $order): Order
    {
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

        $address = new Address();
        $address->attributes = $values;

        $transaction = \Yii::$app->db->beginTransaction();
        if (!$address->save(true)) {
            $transaction->rollBack();
            //TODO: Email errors to customer?
            throw new Exception('New address entry could not be created.' . PHP_EOL . implode(PHP_EOL, $address->getErrorSummary(true)));
        }
        $transaction->commit();

        $order->address_id = $address->id;

        return $order;
    }

    /**
     * @param Order $order
     * @return Order
     */
    private function setShippingInfo(Order $order): Order
    {
        $order->carrier_id = Carrier::findOne(["name" => "FedEx"])->id; //TODO: Add ability to handle different carriers
        $order->service_id = $this->shippingService;
        return $order;
    }

    protected abstract function buildGeneral($json);

    protected abstract function buildAddress($json);

    protected abstract function buildShipping($json);

    protected abstract function buildItems($json);
}