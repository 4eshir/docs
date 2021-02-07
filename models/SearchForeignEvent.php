<?php

namespace app\models;

use Yii;
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
    public $start_date_search;
    public $finish_date_search;
    public $secondnameParticipant;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'order_business_trip_id'], 'integer'],
            [['name', 'start_date', 'finish_date', 'city', 'key_words', 'docs_achievement', 'companyString', 'eventLevelString', 'eventWayString', 'start_date_search', 'finish_date_search', 'secondnameParticipant'], 'safe'],
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
        $str = '###';
        $query = '';
        if (strlen($params["SearchForeignEvent"]["secondnameParticipant"]) > 2)
        {
            $str = "SELECT *  FROM (SELECT *, (SELECT GROUP_CONCAT(CONCAT(`secondname`,' '), CONCAT(LEFT(`firstname`, 1), '.'), CONCAT(LEFT(`patronymic`, 1), '.') SEPARATOR ' ') FROM `foreign_event_participants` WHERE `id` IN 
                    (SELECT `participant_id` FROM `teacher_participant` WHERE `foreign_event_id` = `foreign_event`.`id` AND `secondname` = '".$params["SearchForeignEvent"]["secondnameParticipant"]."')) as `participants` 
                    FROM `foreign_event` WHERE 1) as t1 WHERE `t1`.`participants` IS NOT NULL";
            $query = ForeignEvent::findBySql($str);

        }
        if (strlen($params["SearchForeignEvent"]["start_date_search"]) > 9 && strlen($params["SearchForeignEvent"]["finish_date_search"]) > 9)
        {
            if ($str !== '###')
            {
                $str = "SELECT * FROM (".$str.") as t2 WHERE `t2`.`start_date` >= '".$params["SearchForeignEvent"]["start_date_search"]."' AND `t2`.`start_date` <= '".$params["SearchForeignEvent"]["finish_date_search"]."'";
                $query = ForeignEvent::findBySql($str);
            }
            else
                $query = ForeignEvent::find()->where(['>=', 'start_date', $params["SearchForeignEvent"]["start_date_search"]])->andWhere(['<=', 'start_date', $params["SearchForeignEvent"]["finish_date_search"]]);
        }
        else
        {
            if ($str == '###')
            {
                $query = ForeignEvent::findBySql('SELECT *, '.
                    '(SELECT GROUP_CONCAT(CONCAT(`secondname`,\' \'), CONCAT(LEFT(`firstname`, 1), \'.\'), CONCAT(LEFT(`patronymic`, 1), \'.\') SEPARATOR \' \')'.
                    'FROM `foreign_event_participants` WHERE `id` IN'.
                    '(SELECT `participant_id` FROM `teacher_participant` WHERE `foreign_event_id` = `foreign_event`.`id`)) as `participants`'.
                    'FROM `foreign_event` WHERE 1');
            }

        }
        $query->joinWith(['company company']);
        $query->joinWith(['eventLevel']);
        $query->joinWith(['eventWay']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['participants'] = [
            'asc' => ['foreignEventParticipants.Secondname' => SORT_ASC],
            'desc' => ['foreignEventParticipants.Secondname' => SORT_DESC],
        ];

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
            ->andFilterWhere(['like', 'foreignEventParticipants.Secondname', $this->participants])
            ->andFilterWhere(['like', 'docs_achievement', $this->docs_achievement]);

        return $dataProvider;
    }
}
