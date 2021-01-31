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
    public $companyString;
    public $eventLevelString;
    public $eventWayString;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'order_business_trip_id'], 'integer'],
            [['name', 'start_date', 'finish_date', 'city', 'key_words', 'docs_achievement', 'companyString', 'eventLevelString', 'eventWayString'], 'safe'],
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
        $query->joinWith(['company company']);
        $query->joinWith(['eventLevel']);
        $query->joinWith(['eventWay']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['companyString'] = [
            'asc' => ['company.Name' => SORT_ASC],
            'desc' => ['company.Name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventLevelString'] = [
            'asc' => ['event_level.Name' => SORT_ASC],
            'desc' => ['event_level.Name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventWayString'] = [
            'asc' => ['event_way.Name' => SORT_ASC],
            'desc' => ['event_way.Name' => SORT_DESC],
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
            ->andFilterWhere(['like', 'company.Name', $this->companyString])
            ->andFilterWhere(['like', 'eventLevel.Name', $this->eventLevelString])
            ->andFilterWhere(['like', 'eventWay.Name', $this->eventWayString])
            ->andFilterWhere(['like', 'docs_achievement', $this->docs_achievement]);

        return $dataProvider;
    }
}
