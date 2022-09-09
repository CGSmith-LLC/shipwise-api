<?php

namespace frontend\models\search;

use common\models\ScheduledOrder;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * ScheduledOrderSearch represents the model behind the search form of `common\models\ScheduledOrder`.
 */
class ScheduledOrderSearch extends ScheduledOrder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'status_id'], 'integer'],
            [
                [
                    'order_id',
                    'customer_id',
                    'scheduled_date',
                    'pageSize',
                ],
                'safe',
            ],
        ];
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
        $query = ScheduledOrder::find();
        if (!Yii::$app->user->identity->isAdmin) {
            $query->andOnCondition(['customer_id' => Yii::$app->user->identity->customerIds]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['scheduled_date' => SORT_DESC, 'id' => SORT_DESC],
            ],
        ]);

        return $dataProvider;
    }
}
