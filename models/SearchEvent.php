<?php

namespace app\models;

use app\models\work\EventBranchWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\EventWork;

/**
 * SearchEvent represents the model behind the search form of `app\models\common\Event`.
 */
class SearchEvent extends EventWork
{
    public $eventBranchs;

    public $responsibleString;
    public $eventLevelString;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'event_type_id', 'event_form_id', 'event_level_id', 'participants_count', 'is_federal', 'responsible_id', 'order_id', 'regulation_id', 'eventBranchs'], 'integer'],
            [['start_date', 'finish_date', 'address', 'key_words', 'comment', 'protocol', 'photos', 'reporting_doc', 'other_files', 'name'], 'safe'],
            [['responsibleString'], 'string']
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
        $query = EventWork::find();
        if ($params["SearchEvent"]["eventBranchs"] != null)
        {
            $ebs = EventBranchWork::find()->where(['branch_id' => $params["SearchEvent"]["eventBranchs"]])->all();
            $eIds = [];
            foreach ($ebs as $eb) $eIds[] = $eb->event_id;
            $query = EventWork::find()->where(['IN', 'event.id', $eIds]);
        }

        //SELECT * FROM `event` WHERE `id` IN (SELECT `event_id` FROM `event_branch` WHERE `branch_id` = 2)

        // add conditions that should always apply here

        $query->joinWith(['responsible responsible']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['responsibleString'] = [
            'asc' => ['responsible.short_name' => SORT_ASC],
            'desc' => ['responsible.short_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventLevelString'] = [
            'asc' => ['event_level.shortName' => SORT_ASC],
            'desc' => ['event_level.shortName' => SORT_DESC],
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
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'event_type_id' => $this->event_type_id,
            'event_form_id' => $this->event_form_id,
            'event_level_id' => $this->event_level_id,
            'participants_count' => $this->participants_count,
            'is_federal' => $this->is_federal,
            'responsible_id' => $this->responsible_id,
            'order_id' => $this->order_id,
            'regulation_id' => $this->regulation_id,
        ]);

        $query->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'key_words', $this->key_words])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'protocol', $this->protocol])
            ->andFilterWhere(['like', 'photos', $this->photos])
            ->andFilterWhere(['like', 'responsible.Secondname', $this->responsibleString])
            ->andFilterWhere(['like', 'event_level.name', $this->eventLevelString])
            ->andFilterWhere(['like', 'reporting_doc', $this->reporting_doc])
            ->andFilterWhere(['like', 'other_files', $this->other_files]);

        return $dataProvider;
    }
}
