<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\TemporaryJournal;

/**
 * SearchTemporaryJournal represents the model behind the search form of `app\models\common\TemporaryJournal`.
 */
class SearchTemporaryJournal extends TemporaryJournal
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'material_object_id', 'give_people_id', 'gain_people_id', 'approximate_time', 'branch_id', 'auditorium_id', 'event_id', 'foreign_event_id', 'signed_give', 'signed_gain'], 'integer'],
            [['date_issue', 'date_delivery', 'comment', 'files'], 'safe'],
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
        $query = TemporaryJournal::find();

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
            'material_object_id' => $this->material_object_id,
            'give_people_id' => $this->give_people_id,
            'gain_people_id' => $this->gain_people_id,
            'date_issue' => $this->date_issue,
            'approximate_time' => $this->approximate_time,
            'date_delivery' => $this->date_delivery,
            'branch_id' => $this->branch_id,
            'auditorium_id' => $this->auditorium_id,
            'event_id' => $this->event_id,
            'foreign_event_id' => $this->foreign_event_id,
            'signed_give' => $this->signed_give,
            'signed_gain' => $this->signed_gain,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'files', $this->files]);

        return $dataProvider;
    }
}
