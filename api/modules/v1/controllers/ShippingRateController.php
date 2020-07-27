<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\forms\ShippingRateForm;
use api\modules\v1\models\shipping\ShipmentRateEx;
use Exception;
use Yii;

/**
 * Class RateController
 *
 * @package api\modules\v1\controllers
 *
 * @property \common\models\ApiConsumer $apiConsumer
 */
class ShippingRateController extends ControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'create' => ['POST'],
        ];
    }

    /**
     * @SWG\Post(
     *     path = "/shipping/rates",
     *     tags = { "Shipping" },
     *     summary = "Calculate rates",
     *     description = "Calculates shipping rates and transit times.",
     *
     *     @SWG\Parameter(
     *          name = "ShippingRateForm", in = "body", required = true,
     *          @SWG\Schema( ref = "#/definitions/ShippingRateForm" ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 200,
     *          description = "Successful operation. Response contains an array of calculated rates.",
     *          @SWG\Schema(
     *              type = "array",
     *              @SWG\Items( ref = "#/definitions/CalculatedRate" )
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while calculating rates",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 401,
     *          description = "Impossible to authenticate user",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 422,
     *          description = "Fields are missing or invalid",
     *          @SWG\Schema( ref = "#/definitions/ErrorData" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
     *     }}
     * )
     */

    /**
     * Calculate rates
     *
     * @return ShipmentRateEx[]
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {

        // Build the Shipping Rate Form with the attributes sent in request
        $form             = new ShippingRateForm();
        $form->attributes = $this->request->getBodyParams();

        // Validate ShippingRateForm and its related models, return errors if any
        if (!$form->validateAll()) {
            return $this->unprocessableError($form->getErrorsAll());
        }

        // Build Shipment object
        $shipment = $form->buildShipment($this->apiConsumer);

        // Invoke rate calculation
        try {
            $shipment->rate();
        } catch (Exception $e) {
            Yii::error($e);
            $shipment->addError('plugin', $e->getMessage());
        }

        // Returns shipment errors. This includes carrier API errors if any.
        if ($shipment->hasErrors()) {
            return $this->unprocessableError($shipment->getErrors());
        }

        /** @var ShipmentRateEx[] $rates */
        $rates = $shipment->getRates();

        if (count($rates) > 0) {
            return $this->success($rates);
        } else {
            return $this->errorMessage(400, 'No results to return. Try with different data.');
        }
    }
}