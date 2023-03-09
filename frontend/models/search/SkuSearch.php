<?php

namespace frontend\models\search;

use common\models\Customer;
use common\models\Sku;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SkuSearch represents the model behind the search form of `frontend\models\Sku`.
 */
class SkuSearch extends Sku
{
    public function rules()
    {
        // only fields in rules() are searchable
        return [
            [['customer_id'], 'integer'],
            [['sku', 'name'], 'string'],
            [['excluded'], 'boolean'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Sku::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // setup sorts for sales agent
        $dataProvider->sort->attributes = array_merge($dataProvider->sort->attributes, [
            'customer.name' => [
                'asc' => [Customer::tableName() . '.name' => SORT_ASC],
                'desc' => [Customer::tableName() . '.name' => SORT_DESC],
            ]
        ]);


        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'customer_id', $this->customer_id])
            ->andFilterWhere(['like', 'excluded', $this->excluded])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
