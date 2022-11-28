<?php

namespace app\models;

use app\models\work\CompanyWork;
use app\models\work\PeoplePositionBranchWork;
use app\models\work\PositionWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\PeopleWork;

/**
 * SearchPeople represents the model behind the search form of `app\models\common\People`.
 */
class SearchPeople extends PeopleWork
{
    public $companyName;
    public $positionName;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'position_id'], 'integer'],
            [['firstname', 'secondname', 'patronymic', 'companyName', 'positionName'], 'safe'],
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
        $query = PeopleWork::find();
        $query->joinWith(['company company']);
        $query->joinWith(['position position']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['companyName'] = [
            'asc' => [CompanyWork::tableName().'.name' => SORT_ASC],
            'desc' => [CompanyWork::tableName().'.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['positionName'] = [
            'asc' => [PositionWork::tableName().'.name' => SORT_ASC],
            'desc' => [PositionWork::tableName().'.name' => SORT_DESC],
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
            'position_id' => $this->position_id,
        ]);

        $query->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'secondname', $this->secondname])
            ->andFilterWhere(['like', 'patronymic', $this->patronymic])
            ->andFilterWhere(['like', 'company.name', $this->companyName])
            ->andFilterWhere(['like', 'position.name', $this->positionName]);

        return $dataProvider;
    }
}
