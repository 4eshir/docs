<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\ForeignEvent;

/**
 * SearchForeignEvent represents the model behind the search form of `app\models\common\ForeignEvent`.
 */
class SearchForeignEvent extends ForeignEvent
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'order_business_trip_id'], 'integer'],
            [['name', 'start_date', 'finish_date', 'city', 'key_words', 'docs_achievement'], 'safe'],
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
        $query = ForeignEvent::find();

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
            'company_id' => $this->company_id,
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'event_way_id' => $this->event_way_id,
            'event_level_id' => $this->event_level_id,
            'min_participants_age' => $this->min_participants_age,
            'max_participants_age' => $this->max_participants_age,
            'business_trip' => $this->business_trip,
            'escort_id' => $this->escort_id,
            'order_participation_id' => $this->order_participation_id,
            'order_business_trip_id' => $this->order_business_trip_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'key_words', $this->key_words])
            ->andFilterWhere(['like', 'docs_achievement', $this->docs_achievement]);

        return $dataProvider;
    }
}
