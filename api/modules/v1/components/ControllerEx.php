<?php

namespace api\modules\v1\components;

use api\modules\v1\models\core\AddressEx;
use api\modules\v1\models\core\ApiConsumerEx;
use api\modules\v1\models\forms\OrderForm;
use api\modules\v1\models\order\ItemEx;
use api\modules\v1\models\order\OrderEx;
use api\modules\v1\models\order\PackageEx;
use common\models\Order;
use common\models\PackageItem;
use common\models\PackageItemLotInfo;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use Yii;

/**
 * Class ControllerEx
 *
 * @package api\modules\v1\components
 */
class ControllerEx extends Controller
{

    /** @var  \yii\web\Request */
    public $request;

    /** @var  \yii\web\Response */
    public $response;

    /**
     * API Consumer
     *
     * @var \api\modules\v1\models\core\ApiConsumerEx
     */
    public $apiConsumer;

    /** @inheritdoc */
    public function init()
    {
        parent::init();

        $this->request = Yii::$app->request;
        $this->response = Yii::$app->response;
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => 'yii\filters\auth\HttpBasicAuth',
                'auth' => [$this, 'auth'],
            ],
        ]);
    }

    /**
     * @param OrderForm $orderForm
     * @return array|array[]|string[]
     */
    protected function orderCreate(OrderForm $orderForm)
    {
        // Validate OrderForm and its related models, return errors if any
        if (!$orderForm->validateAll()) {
            return $this->unprocessableError($orderForm->getErrorsAll());
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {

            // @Todo - lookup address and set for ID if it matches.

            /**
             * Create Address.
             * At this stage the required shipTo object should be fully validated.
             */
            $address = new AddressEx();
            $address->loadDefaultValues();
            $address->name = $orderForm->shipTo->name;
            $address->company = $orderForm->shipTo->company;
            $address->email = $orderForm->shipTo->email;
            $address->address1 = $orderForm->shipTo->address1;
            $address->address2 = $orderForm->shipTo->address2;
            $address->city = $orderForm->shipTo->city;
            $address->state_id = $orderForm->shipTo->stateId;
            $address->zip = $orderForm->shipTo->zip;
            $address->phone = $orderForm->shipTo->phone;
            $address->notes = $orderForm->shipTo->notes;
            $address->country = $orderForm->shipTo->country;
            $address->save();


            // Create Order
            $order = new OrderEx();
            $order->customer_id = $this->apiConsumer->customer->id;
            $order->notes = $orderForm->notes;
            $order->uuid = $orderForm->uuid;
            $order->po_number = $orderForm->poNumber;
            $order->origin = $orderForm->origin;
            $order->carrier_id = $orderForm->carrier_id;
            $order->service_id = $orderForm->service_id;
            $order->order_reference = $orderForm->orderReference;
            $order->customer_reference = $orderForm->customerReference;
            $order->requested_ship_date = $orderForm->requestedShipDate;
            $order->notes = $orderForm->notes;
            $order->status_id = isset($orderForm->status) ? $orderForm->status : null;
            $order->address_id = $address->id;
            \Yii::debug($order);

            // Validate the order model itself
            if (!$order->validate()) {
                // if you get here then you should check if you have enough OrderForm validation rules
                $transaction->rollBack();

                return $this->unprocessableError($order->getErrors());
            }

            // Create TrackingInfo
            if (!empty($orderForm->tracking)) {
                /**
                 * This is in preparation of the future transition of
                 * tracking details into tracking_info DB table:
                 *
                 * Until the transition not happened, we save tracking number into orders.tracking field.
                 * Once the transition happens, save the full tracking object into tracking_info DB table.
                 * See below.
                 *
                 */
                // The actual "before transition" behaviour:
                $order->tracking = $orderForm->tracking->trackingNumber;

                // The behaviour to implement after transition:
                /*
                $tracking             = new TrackingInfoEx();
                $tracking->service_id = $orderForm->tracking->serviceId;
                $tracking->tracking   = $orderForm->tracking->trackingNumber;
                if ($tracking->save()) {
                    $order->tracking_id = $tracking;
                }
                */
            }

            // Save Order model
            if (!$order->save()) {
                $transaction->rollBack();

                return $this->errorMessage(400, 'Could not save order');
            }

            /**
             * Create Items.
             * At this stage the required items array should be fully validated.
             */
            foreach ($orderForm->items as $formItem) {
                $item = new ItemEx();
                $item->order_id = $order->id;
                $item->uuid = $formItem->uuid;
                $item->sku = $formItem->sku;
                $item->quantity = $formItem->quantity;
                $item->alias_sku = $formItem->alias_sku;
                $item->alias_quantity = $formItem->alias_quantity;
                $item->name = $formItem->name;
                $item->save();
            }

            // Commit DB transaction
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not save order');
        } catch (\Throwable $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not save order');
        }

        $order->refresh();

        return $this->success($order, 201);
    }

    /**
     * Authenticates user.
     *
     * This function is used by HttpBasicAuth yii authenticator.
     * Finds user by username and password (db fields: auth_secret and auth_token)
     *
     * @param OrderForm $orderForm
     * @param string $id
     *
     * @return array|string[]
     */

    public function orderUpdate(OrderForm $orderForm, $id)
    {

        // Validate OrderForm and its related models, return errors if any
        if (!$orderForm->validateAll()) {
            return $this->unprocessableError($orderForm->getErrorsAll());
        }

        // Find the order to update
        if (($order = OrderEx::find()
                ->byId($id)
                ->forCustomer($this->apiConsumer->customer->id)
                ->one()
            ) === null) {
            return $this->errorMessage(404, 'Order not found');
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {

            /**
             * Update Address.
             * At this stage the required shipTo object should be fully validated.
             */
            if (($address = AddressEx::findOne($order->address_id)) !== null) {
                $address->name = $orderForm->shipTo->name;
                $address->address1 = $orderForm->shipTo->address1;

                if (isset($orderForm->shipTo->address2) && !empty($orderForm->shipTo->address2)) {
                    $address->address2 = $orderForm->shipTo->address2;
                }

                $address->city = $orderForm->shipTo->city;
                $address->state_id = $orderForm->shipTo->stateId;
                $address->zip = $orderForm->shipTo->zip;
                $address->country = $orderForm->shipTo->country;

                if (isset($orderForm->shipTo->phone) && !empty($orderForm->shipTo->phone)) {
                    $address->phone = $orderForm->shipTo->phone;
                }

                if (isset($orderForm->shipTo->notes) && !empty($orderForm->shipTo->notes)) {
                    $address->notes = $orderForm->shipTo->notes;
                }

                $address->save();
            }

            // Is ShipFrom address set from order form?
            // @TODO fix the duplication from above
            if (isset($orderForm->shipFrom)) {

                $order->ship_from_name = $orderForm->shipFrom->name;
                $order->ship_from_address1 = $orderForm->shipFrom->address1;
                $order->ship_from_address2 = $orderForm->shipFrom->address2;
                $order->ship_from_city = $orderForm->shipFrom->city;
                $order->ship_from_state_id = $orderForm->shipFrom->stateId;
                $order->ship_from_zip = $orderForm->shipFrom->zip;
                $order->ship_from_country_code = $orderForm->shipFrom->country;
                $order->ship_from_phone = $orderForm->shipFrom->phone;
                $order->ship_from_email = $orderForm->shipFrom->email;
            }

            /**
             * Update carrier information
             */
            if (!empty($orderForm->carrier_id)) {
                $order->carrier_id = $orderForm->carrier_id;
            }
            if (!empty($orderForm->service_id)) {
                $order->service_id = $orderForm->service_id;
            }

            /**
             * Update TrackingInfo.
             * At this stage the required tracking object should be fully validated.
             */
            if (!empty($orderForm->tracking)) {
                /**
                 * This is in preparation of the future transition of
                 * tracking details into tracking_info DB table:
                 *
                 * Until the transition not happened, we save tracking number into orders.tracking field.
                 * Once the transition happens, save the full tracking object into tracking_info DB table.
                 * See below.
                 *
                 */
                // The actual "before transition" behaviour:
                $order->tracking = $orderForm->tracking->trackingNumber;

                // The behaviour to implement after transition:
                /*
                $tracking             = new TrackingInfoEx();
                $tracking->service_id = $orderForm->tracking->serviceId;
                $tracking->tracking   = $orderForm->tracking->trackingNumber;
                if ($tracking->save()) {
                    $order->tracking_id = $tracking;
                }
                */
            }

            /**
             * Update Items.
             * At this stage the required items array should be fully validated.
             */
            ItemEx::deleteAll(['order_id' => $order->id]);
            foreach ($orderForm->items as $formItem) {
                $item = new ItemEx();
                $item->order_id = $order->id;
                $item->sku = $formItem->sku;
                $item->uuid = $formItem->uuid;
                $item->quantity = $formItem->quantity;
                $item->name = $formItem->name;
                $item->save();
            }

            /**
             * Update order.
             */
            $order->notes = $orderForm->notes;
            $order->order_reference = $orderForm->orderReference;
            $order->po_number = $orderForm->poNumber;
            $order->customer_reference = $orderForm->customerReference;
            $order->status_id = $orderForm->status;

            // Validate the order model itself
            if (!$order->validate()) {
                // if you get here then you should check if you have enough OrderForm validation rules
                $transaction->rollBack();

                return $this->unprocessableError($order->getErrors());
            }

            // Packages

            if (!empty($orderForm->packages)) {
                PackageEx::deleteAll(['order_id' => $order->id]);
                foreach ($orderForm->packages as $formPackage) {
                    $package = new PackageEx();
                    $package->setAttribute('length', $formPackage['length']);
                    $package->setAttribute('width', $formPackage['width']);
                    $package->setAttribute('height', $formPackage['height']);
                    if (isset($formPackage['weight'])) {
                        $package->setAttribute('weight', $formPackage['weight']);
                    }
                    if (isset($formPackage['tracking']) && !is_null($formPackage['tracking'])) {
                        $package->setAttribute('tracking', $formPackage['tracking']);
                    }
                    $package->setAttribute('order_id', $order->id);
                    $package->save();
                    if (is_array($formPackage['package_items'])) {
                        foreach ($formPackage['package_items'] as $package_item) {
                            $packageItem = new PackageItem();
                            $packageItem->setAttribute('quantity', $package_item['quantity']);
                            $packageItem->setAttribute('sku', $package_item['sku']);
                            $packageItem->setAttribute('name', $package_item['name']);
                            $packageItem->setAttribute('package_id', $package->id);
                            $packageItem->setAttribute('order_id', $order->id);
                            $packageItem->save();
                            if (isset($package_item['lot_info'])) {
                                foreach ($package_item['lot_info'] as $lot_info) {
                                    $lotInfo = new PackageItemLotInfo();
                                    $lotInfo->setAttribute('quantity', $lot_info['quantity']);
                                    $lotInfo->setAttribute('lot_number', $lot_info['lot_number']);
                                    $lotInfo->setAttribute('serial_number', $lot_info['serial_number']);
                                    $lotInfo->setAttribute('package_items_id', $packageItem->id);
                                    $lotInfo->save();
                                }
                            }
                        }
                    }
                }
            }

            // Save Order model
            if (!$order->save()) {
                $transaction->rollBack();

                return $this->errorMessage(400, 'Could not save order');
            }

            // Commit DB transaction
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not save order');
        } catch (\Throwable $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not save order');
        }

        $order->refresh();

        return $this->success($order);
    }


    public function auth($username, $password)
    {
        if (empty($username) || empty($password)) {
            return null;
        }

        // Find user
        if (($this->apiConsumer = ApiConsumerEx::findByKeySecret($username, $password)) === null) {
            return null;
        }

        // Check if user is active
        if (!$this->apiConsumer->isActive()) {
            return null;
        }

        /**
         * Set user identity without touching session or cookie.
         * (this is preferred use in stateless RESTful API implementation)
         */
        Yii::$app->user->setIdentity($this->apiConsumer);

        /**
         * User successfully authenticated.
         *
         * @see yii\web\User
         *
         * Yii::$app->user->identity to access currently authenticated user.
         * Yii::$app->user->identity->customer to access currently authenticated customer if any.
         *
         */

        // Log user activity
        $this->apiConsumer->updateLastActivity()->save();

        return $this->apiConsumer;
    }

    /**
     * Successful response
     *
     * This function sets the response status code 200
     * and returns the response.
     *
     * @param $response
     * @param $code
     *
     * @return array
     */
    public function success($response, $code = 200)
    {
        $this->response->setStatusCode($code);

        return $response;
    }

    /**
     * Error response
     *
     * @param int    $code    HTTP code
     * @param string $message Error message
     *
     * @return array
     */
    public function errorMessage($code, $message)
    {
        $this->response->setStatusCode($code);

        return ['message' => $message];
    }

    /**
     * Define the response status code to 422 (unprocessable entity) and return the errors.
     *
     * @param array $data
     *
     * @return array
     */
    public function unprocessableError($data)
    {
        $this->response->setStatusCode(422);

        return ['errors' => $data];
    }
}