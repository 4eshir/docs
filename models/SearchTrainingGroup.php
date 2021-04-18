<?php

namespace app\models;

use app\models\common\User;
use app\models\components\UserRBAC;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\TrainingGroup;

/**
 * SearchTrainingGroup represents the model behind the search form of `app\models\common\TrainingGroup`.
 */
class SearchTrainingGroup extends TrainingGroup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'number', 'training_program_id', 'teacher_id', 'open'], 'integer'],
            [['start_date', 'finish_date', 'photos', 'present_data', 'work_data'], 'safe'],
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
        $query = TrainingGroup::find();
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), 'index', 'training-group'))
        {
            $user = User::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
            $query = TrainingGroup::find()->where(['teacher_id' => $user->aka]);
        }


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
            'number' => $this->number,
            'training_program_id' => $this->training_program_id,
            'teacher_id' => $this->teacher_id,
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'open' => $this->open,
        ]);

        $query->andFilterWhere(['like', 'photos', $this->photos])
            ->andFilterWhere(['like', 'present_data', $this->present_data])
            ->andFilterWhere(['like', 'work_data', $this->work_data]);

        return $dataProvider;
    }
}
