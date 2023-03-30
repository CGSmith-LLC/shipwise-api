<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\customer\CustomerEx;
use api\modules\v1\models\forms\CustomerForm;
use yii\web\ForbiddenHttpException;

/**
 * Class CustomerController
 *
 * @package api\modules\v1\controllers
 */
class CustomerController extends ControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'index'  => ['GET'],
            'create' => ['POST'],
            'update' => ['PUT'],
            'view'   => ['GET'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * Get all customers
     *
     * @return \api\modules\v1\models\customer\CustomerEx[]
     * @throws ForbiddenHttpException
     *
     * @SWG\Get(
     *     path = "/customers",
     *     tags = { "Customers" },
     *     summary = "Fetch all customers",
     *     description = "Get all customers",
     *
     *     @SWG\Response(
     *          response = 200,
     *          description = "Successful operation. Response contains a list of customers.",
     *          @SWG\Schema(
     *              type = "array",
     *              @SWG\Items( ref = "#/definitions/Customer" )
     *            ),
     *     ),
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
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {},
     *     }}
     * )
     * @todo Paginate results? If so, @see OrderController::actionIndex() for reference.
     *
     */
    public function actionIndex()
    {
        // Check permissions
        if (!$this->apiConsumer->isSuperuser()) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        return $this->success(
            CustomerEx::find()->all()
        );
    }

    /**
     * @SWG\Post(
     *     path = "/customers",
     *     tags = { "Customers" },
     *     summary = "Create new customer",
     *     description = "Creates new customer",
     *
     *     @SWG\Parameter(
     *          name = "CustomerForm", in = "body", required = true,
     *          @SWG\Schema( ref = "#/definitions/CustomerForm" ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 201,
     *          description = "Customer created successfully",
     *          @SWG\Schema(
     *              ref = "#/definitions/Customer"
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while creating customer",
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
     * Create new customer
     *
     * @return array|\api\modules\v1\models\customer\CustomerEx
     * @throws \yii\base\InvalidConfigException
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): array|\api\modules\v1\models\customer\CustomerEx
    {
        // Check permissions
        if (!$this->apiConsumer->isSuperuser()) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        // Build the Customer Form with the attributes sent in request
        $form             = new CustomerForm();
        $form->attributes = $this->request->getBodyParams();

        // Validate that all rules are respected
        if (!$form->validate()) {
            return $this->unprocessableError($form->getErrors());
        }

        // Create new customer
        $customer = new CustomerEx();
        $customer->setAttributes($form->attributes);

        // Validate the customer model itself
        if (!$customer->validate()) {
            // if you get here then you should add more validation rules to CustomerForm
            return $this->unprocessableError($customer->getErrors());
        }

        if ($customer->save()) {
            $customer->refresh();

            return $this->success($customer);
        } else {
            return $this->errorMessage(400, 'Could not save customer');
        }
    }

    /**
     * @SWG\Get(
     *     path = "/customers/{id}",
     *     tags = { "Customers" },
     *     summary = "Fetch a specific customer",
     *     description = "Fetch a specific customer with full details by ID",
     *
     *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
     *
     *     @SWG\Response(
     *          response = 200,
     *          description = "Successful operation. Response contains the customer found.",
     *          @SWG\Schema(
     *              ref = "#/definitions/Customer"
     *            ),
     *     ),
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
     *          response = 404,
     *          description = "Customer not found",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
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
     * Get a specific customer
     *
     * @param int $id Customer ID
     *
     * @return array|\api\modules\v1\models\customer\CustomerEx
     * @throws ForbiddenHttpException
     */
    public function actionView($id): array|\api\modules\v1\models\customer\CustomerEx
    {
        // Check permissions
        if (!$this->apiConsumer->isSuperuser()) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        if (($customer = CustomerEx::findOne((int)$id)) !== null) {
            return $this->success($customer);
        } else {
            return $this->errorMessage(404, 'Customer not found');
        }
    }

    /**
     * @SWG\Put(
     *     path = "/customers/{id}",
     *     tags = { "Customers" },
     *     summary = "Update a specific customer",
     *     description = "Updates an existing customer",
     *
     *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
     *
     *     @SWG\Parameter(
     *          name = "CustomerForm", in = "body", required = true,
     *          @SWG\Schema( ref = "#/definitions/CustomerForm" ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 200,
     *          description = "Successful operation. Response contains updated customer.",
     *          @SWG\Schema(
     *              ref = "#/definitions/Customer"
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while updating customer",
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
     *          response = 404,
     *          description = "Customer not found",
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
     * Update customer
     *
     * @param int $id Customer ID
     *
     * @return array|\api\modules\v1\models\customer\CustomerEx
     * @throws \yii\base\InvalidConfigException
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id): array|\api\modules\v1\models\customer\CustomerEx
    {

        // Check permissions
        if (!$this->apiConsumer->isSuperuser()) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        // Build the Customer Form with the attributes sent in request
        $form             = new CustomerForm();
        $form->attributes = $this->request->getBodyParams();

        // Validate that all rules are respected
        if (!$form->validate()) {
            return $this->unprocessableError($form->getErrors());
        }

        // Find the customer to update
        if (($customer = CustomerEx::findOne((int)$id)) === null) {
            return $this->errorMessage(404, 'Customer not found');
        }
        $customer->setAttributes($form->attributes);

        // Validate the customer model itself
        if (!$customer->validate()) {
            // if you get here then you should add more validation rules to CustomerForm
            return $this->unprocessableError($customer->getErrors());
        }

        // Save customer
        if ($customer->save()) {
            $customer->refresh();

            return $this->success($customer);
        } else {
            return $this->errorMessage(400, 'Could not save customer');
        }
    }

    /**
     * @SWG\Delete(
     *     path = "/customers/{id}",
     *     tags = { "Customers" },
     *     summary = "Delete a customer",
     *     description = "Deletes a specific customer",
     *
     *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
     *
     *     @SWG\Response(
     *          response = 204,
     *          description = "Customer deleted successfully",
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while deleting customer",
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
     *          response = 404,
     *          description = "Customer not found",
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
     * Delete customer
     *
     * @param int $id Customer ID
     *
     * @return array|void
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        // Check permissions
        if (!$this->apiConsumer->isSuperuser()) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        // Find the customer to delete
        if (($customer = CustomerEx::findOne((int)$id)) === null) {
            return $this->errorMessage(404, 'Customer not found');
        }

        // Delete customer
        if (!$customer->delete()) {
            return $this->errorMessage(400, 'Could not delete customer');
        }

        $this->response->setStatusCode(204);
    }
}