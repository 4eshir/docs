<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\TrainingProgramWork;

/**
 * SearchTrainingProgram represents the model behind the search form of `app\models\common\TrainingProgram`.
 */
class SearchTrainingProgram extends TrainingProgramWork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'author_id', 'capacity', 'student_left_age', 'student_right_age', 'focus', 'allow_remote'], 'integer'],
            [['name', 'ped_council_date', 'ped_council_number', 'doc_file', 'edit_docs', 'key_words'], 'safe'],
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
        $query = TrainingProgramWork::find()->orderBy(['actual' => SORT_DESC]);

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
            'ped_council_date' => $this->ped_council_date,
            'author_id' => $this->author_id,
            'capacity' => $this->capacity,
            'student_left_age' => $this->student_left_age,
            'student_right_age' => $this->student_right_age,
            'focus' => $this->focus,
            'allow_remote' => $this->allow_remote,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'ped_council_number', $this->ped_council_number])
            ->andFilterWhere(['like', 'doc_file', $this->doc_file])
            ->andFilterWhere(['like', 'edit_docs', $this->edit_docs])
            ->andFilterWhere(['like', 'key_words', $this->key_words]);

        return $dataProvider;
    }
}
