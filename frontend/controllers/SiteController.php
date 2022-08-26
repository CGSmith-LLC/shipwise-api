<?php

namespace frontend\controllers;

use common\models\Status;
use frontend\models\Customer;
use frontend\models\forms\DashboardForm;
use frontend\models\forms\ReportForm;
use frontend\models\Order;
use Yii;
use yii\base\BaseObject;
use yii\base\Response;
use yii\db\Expression;
use yii\web\Controller;
use DateTime;
use yii\web\Request;

/**
 * Site controller
 */
class SiteController extends \frontend\controllers\Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    /**
     * Index for the dashboard numbers
     *
     * @inheritDoc
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionJson()
    {
        /**
         * gets default start and end dates of query
         */
        $defaultStart = new DateTime('now');
        $defaultStart->modify('-7 day');
        $defaultStart = $defaultStart->format('Y-m-d 00:00:00');
        $defaultEnd = new DateTime('now');
        $defaultEnd = $defaultEnd->format('Y-m-d 23:59:59');
        /**
         * gets inputted start and end dates of query
         */
        $request = Yii::$app->request;
        $start_date = Yii::$app->formatter->asDate($request->get('start_date', $defaultStart), 'php:Y-m-d 00:00:00');
        $end_date = Yii::$app->formatter->asDate($request->get('end_date', $defaultEnd), 'php:Y-m-d 23:59:59');

        $statusArray = [Status::OPEN, Status::PENDING, Status::SHIPPED, Status::COMPLETED, Status::WMS_ERROR];
        $query = (new \yii\db\Query())
            ->select(['status.name as status', 'customers.name as customer', 'orders.customer_id', 'orders.status_id', 'COUNT(*) as shipments'])
            ->from('orders')
            ->leftJoin('customers', 'orders.customer_id = customers.id')
            ->leftJoin('status', 'orders.status_id = status.id')
            ->where(['between', 'orders.created_date', $start_date, $end_date])
            ->andWhere(['in', 'orders.customer_id', $this->customer_ids])
            ->andWhere(['in', 'orders.status_id', $statusArray])
            ->groupBy(['customer_id', 'status_id'])
            ->orderBy('customer_id')
            ->all();
        $customers = Customer::find()->where(['in', 'id', $this->customer_ids])->all();
        /**
         * @var Customer $customer
         */
        $statuses = Status::find()
            ->where(['in', 'id', $statusArray])
            ->orderBy([new Expression('FIELD(id, 9, 8, 1, 11, 10)')])
            ->all();

        /**
         * @var Status $status
         */
        foreach ($statuses as $status) {
            $status2[$status->id] = [
                'slug' => $this->lookupSlug($status->id),
                'colwidth' => $this->lookupColumn($status->id),
                'name' => $status->name,
                'orders' => 0,
            ];
        }
        $response = [];
        foreach ($customers as $customer) {
            $response[$customer->id] = [
                'name' => $this->trimName($customer->name),
                'avatar' => $this->lookupAvatar($customer->name),
                'avatarcolor' => $this->lookupAvatarColor($this->lookupAvatar($customer->name)),
                'customer_id' => $customer->id,
                'statuses' => $status2,
            ];
            foreach ($query as $q) {
                $response[$q['customer_id']]['statuses'][$q['status_id']]['orders'] = (int)$q['shipments'];
            }
        }

        foreach ($response as $key => $res) {
            $statues = $res['statuses'];
            unset($response[$key]['statuses']);
            foreach ($statues as $status => $value) {
                $response[$key]['statuses'][] = $value;
            }
        }

        Yii::debug($response);

        return $this->asJson($response);
    }

    private function lookupAvatar($name)
    {
        $exploded = explode(' ', $name);

        if (isset($exploded[1])) {
            $return = substr($name, 0, 1) . substr($exploded[1], 0, 1);
        }
        $return = substr($name, 0, 1);

        return strtoupper($return);
    }

    public function actionConway()
    {
        return $this->render('conway', [
                'name'   => 'Conway',
                'width'  => '',
                'height' => '',
            ]);
    }

    private function lookupAvatarColor($avatar)
    {
        $colors = ['#00AA55', '#009FD4', '#B381B3', '#939393', '#E3BC00', '#D47500', '#DC2A2A'];
        $int = ord($avatar);

        return $colors[$int % count($colors)];
    }

    private function trimName($name)
    {
        if (strlen($name) > 12) {
            $name = substr($name, 0, 12) . '...';
        }

        return $name;
    }

    private function lookupSlug($id)
    {
        switch ($id) {
            case Status::WMS_ERROR:
                return 'red';
            case Status::COMPLETED:
                return 'green';
            default:
                return 'blue';
        }
    }
    private function lookupColumn($id)
    {
        switch ($id) {
            case Status::WMS_ERROR:
                return '1';
            case Status::COMPLETED:
                return '2';
            default:
                return '1';
        }
    }
}