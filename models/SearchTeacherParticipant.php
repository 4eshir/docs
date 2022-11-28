<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\TeacherParticipantWork;

/**
 * SearchTeacherParticipant represents the model behind the search form of `app\models\common\TeacherParticipant`.
 */
class SearchTeacherParticipant extends TeacherParticipantWork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'participant_id', 'teacher_id', 'foreign_event_id', 'branch_id'], 'integer'],
            [['focus'], 'safe'],
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
        $query = TeacherParticipantWork::find();

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
            'participant_id' => $this->participant_id,
            'teacher_id' => $this->teacher_id,
            'foreign_event_id' => $this->foreign_event_id,
            'branch_id' => $this->branch_id,
        ]);

        $query->andFilterWhere(['like', 'focus', $this->focus]);

        return $dataProvider;
    }
}
