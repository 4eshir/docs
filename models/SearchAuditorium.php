<?php

namespace app\models;

use app\models\common\Branch;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\Auditorium;

/**
 * SearchAuditorium represents the model behind the search form of `app\models\common\Auditorium`.
 */
class SearchAuditorium extends Auditorium
{
    public $branchName;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_education', 'branch_id'], 'integer'],
            [['name', 'text', 'files', 'branchName'], 'safe'],
            [['square'], 'number'],
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
        $query = Auditorium::find();
        $query->joinWith(['branch branch']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['branchName'] = [
            'asc' => [Branch::tableName().'.name' => SORT_ASC],
            'desc' => [Branch::tableName().'.name' => SORT_DESC],
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
            'square' => $this->square,
            'is_education' => $this->is_education,
            'branch_id' => $this->branch_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'branch.name', $this->branchName])
            ->andFilterWhere(['like', 'files', $this->files]);

        return $dataProvider;
    }
}
