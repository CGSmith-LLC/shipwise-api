<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
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
                    'tracking',
                    'created_date',
                    'updated_date',
                    'notes',
                    'uuid',
                    'requested_ship_date',
                    'origin'
                ],
                'safe'
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

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'status_id' => $this->status_id,
            'created_date' => $this->created_date,
            'updated_date' => $this->updated_date,
            'address_id' => $this->address_id,
            'requested_ship_date' => $this->requested_ship_date,
            'carrier_id' => $this->carrier_id,
            'service_id' => $this->service_id,
        ]);

        $query->andFilterWhere(['like', 'order_reference', $this->order_reference])
            ->andFilterWhere(['like', 'customer_reference', $this->customer_reference])
            ->andFilterWhere(['like', 'tracking', $this->tracking])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'origin', $this->origin]);

        return $dataProvider;
    }
}
