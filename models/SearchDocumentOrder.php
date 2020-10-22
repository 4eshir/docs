<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\DocumentOrder;

/**
 * SearchDocumentOrder represents the model behind the search form of `app\models\common\DocumentOrder`.
 */
class SearchDocumentOrder extends DocumentOrder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_number', 'signed_id', 'bring_id', 'executor_id', 'scan', 'register_id'], 'integer'],
            [['order_name', 'order_date'], 'safe'],
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
        $query = DocumentOrder::find();

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
            'order_number' => $this->order_number,
            'order_date' => $this->order_date,
            'signed_id' => $this->signed_id,
            'bring_id' => $this->bring_id,
            'executor_id' => $this->executor_id,
            'scan' => $this->scan,
            'register_id' => $this->register_id,
        ]);

        $query->andFilterWhere(['like', 'order_name', $this->order_name]);

        return $dataProvider;
    }
}
