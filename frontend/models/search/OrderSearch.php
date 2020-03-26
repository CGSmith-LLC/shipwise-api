<?php

namespace frontend\models\search;

use common\models\Address;
use common\models\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Order;

/**
 * OrderSearch represents the model behind the search form of `frontend\models\Order`.
 */
class OrderSearch extends Order
{

    /**
     * PO Number filter
     *
     * @var string
     */
    public $po_number;

    /**
     * Address name filter
     *
     * @var string
     */
    public $address;

    /**
     * Page Size
     * Nb rows per page
     *
     * @var int
     */
    public $pageSize = 10;

    /**
     * Page size option
     *
     * @var array
     */
    public $pageSizeOptions = [
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100',
        '500' => '500',
        //'-1'  => 'All', // Use with precaution. Large dataset might crash web browser.
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'status_id', 'address_id', 'carrier_id', 'service_id'], 'integer'],
            [
                [
                    'order_reference',
                    'customer_reference',
                    'po_number',
                    'tracking',
                    'created_date',
                    'updated_date',
                    'notes',
                    'uuid',
                    'requested_ship_date',
                    'origin',
                    'address',
                    'pageSize',
                ],
                'safe',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();

        // Set order search to a session
        if (!isset($params["OrderSearch"])) {
            if (isset(Yii::$app->session["ordersearch"])) {
                $params["OrderSearch"] = Yii::$app->session["ordersearch"];
            }
        } else {
            Yii::$app->session["ordersearch"] = $params["OrderSearch"];
        }
        // add conditions that should always apply here

        if (!isset($params['OrderSearch']['status_id'])) {
            $this->status_id = Status::OPEN;
            $query->byStatus($this->status_id);
        }
        // If user is not admin, then show orders that ONLY belong to current user
        if (!Yii::$app->user->identity->isAdmin) {
            $query->forCustomers(Yii::$app->user->identity->customerIds);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_date' => SORT_DESC, 'id' => SORT_DESC],
            ],
        ]);

        // Set up sorting for join tables
        $dataProvider->sort->attributes['address'] = [
            'asc' => [Address::tableName() . '.name' => SORT_ASC],
            'desc' => [Address::tableName() . '.name' => SORT_DESC],
        ];

        $this->load($params);

        // For a better DB query performance, only join relations if user is searching or sorting related models.
        if ($this->address || (isset($params['sort']) && strpos($params['sort'], 'address') !== false)) {
            $query->joinWith(['address']);
        }

        $dataProvider->pagination->pageSize = $this->pageSize;

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Order::tableName() . '.id' => $this->id,
            'po_number' => $this->po_number,
            'customer_id' => $this->customer_id,
            'status_id' => $this->status_id,
            //'address_id'               => $this->address_id,
            'carrier_id' => $this->carrier_id,
            'service_id' => $this->service_id,
        ]);

        $query->andFilterWhere(['like', 'order_reference', $this->order_reference])
            ->andFilterWhere(['like', 'customer_reference', $this->customer_reference])
            ->andFilterWhere(['like', 'tracking', $this->tracking])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'origin', $this->origin])
            ->andFilterWhere(['like', 'requested_ship_date', $this->requested_ship_date])
            ->andFilterWhere(['like', Order::tableName() . '.created_date', $this->created_date])
            ->andFilterWhere(['like', Order::tableName() . '.updated_date', $this->updated_date])
            ->andFilterWhere(['like', Address::tableName() . '.name', $this->address]);

        return $dataProvider;
    }
}
