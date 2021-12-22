<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\RegulationWork;

/**
 * SearchRegulation represents the model behind the search form of `app\models\common\Regulation`.
 */
class SearchRegulation extends RegulationWork
{
    public $orderString;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'ped_council_number', 'par_council_number', 'state'], 'integer'],
            [['orderString'], 'string'],
            [['date', 'name', 'ped_council_date', 'par_council_date', 'scan'], 'safe'],
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
    public function search($params, $c)
    {
        $query = RegulationWork::find()->where(['regulation_type_id' => $c]);

        // add conditions that should always apply here
        $query->joinWith(['order order']);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['orderString'] = [
            'asc' => ['order.order_name' => SORT_ASC],
            'desc' => ['order.order_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'date' => $this->date,
            'order_id' => $this->order_id,
            'ped_council_number' => $this->ped_council_number,
            'ped_council_date' => $this->ped_council_date,
            'par_council_number' => $this->par_council_number,
            'par_council_date' => $this->par_council_date,
            'regulation.state' => $this->state,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'order.order_name', $this->orderString])
            ->andFilterWhere(['like', 'scan', $this->scan]);

        return $dataProvider;
    }
}
