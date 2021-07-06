<?php

namespace app\models;

use app\models\work\MaterialObjectWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\MaterialObject;

/**
 * SearchMaterialObject represents the model behind the search form of `app\models\common\MaterialObject`.
 */
class SearchMaterialObject extends MaterialObjectWork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'count', 'main'], 'integer'],
            [['unique_id', 'name', 'acceptance_date', 'files'], 'safe'],
            [['balance_price'], 'number'],
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
        $query = MaterialObjectWork::find();

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
            'acceptance_date' => $this->acceptance_date,
            'balance_price' => $this->balance_price,
            'count' => $this->count,
            'main' => $this->main,
        ]);

        $query->andFilterWhere(['like', 'unique_id', $this->unique_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'files', $this->files]);

        return $dataProvider;
    }
}
