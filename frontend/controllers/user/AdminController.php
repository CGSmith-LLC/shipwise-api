<?php

namespace frontend\controllers\user;

use dektrium\user\controllers\AdminController as BaseAdminController;
use frontend\models\Customer;
use frontend\models\search\CustomerSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class AdminController
 *
 * @package frontend\controllers\user
 */
class AdminController extends BaseAdminController
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class'   => 'yii\filters\VerbFilter',
                'actions' => [
                    'link-customer' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Shows a list of all customers and allows admin to associate one or many customers to user.
     *
     * @param int $id User ID
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionAssociateCustomers($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);

        $searchModel  = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('_associate-customers', [
            'user'         => $user,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Link/Unlink the user to/from given customer.
     *
     * @param int $id  User ID
     * @param int $cid Customer ID
     *
     * @return Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionLinkCustomer($id, $cid)
    {
        $user = $this->findModel((int)$id);
        if (($customer = Customer::findOne((int)$cid)) === null) {
            throw new NotFoundHttpException('Customer does not exist');
        }

        if ($customer->isLinkedToUser($user->id)) {
            $user->unlink('customers', $customer, true);
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Customer has been unlinked.'));
        } else {
            $user->link('customers', $customer);
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Customer has been linked.'));
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }
}