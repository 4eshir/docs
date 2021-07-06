<?php

namespace app\models;

use app\models\work\PeopleMaterialObjectWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\PeopleMaterialObject;

/**
 * SearchPeopleMaterialObject represents the model behind the search form of `app\models\common\PeopleMaterialObject`.
 */
class SearchPeopleMaterialObject extends PeopleMaterialObjectWork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'people_id', 'material_object_id'], 'integer'],
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
        $query = PeopleMaterialObjectWork::find();

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
            'people_id' => $this->people_id,
            'material_object_id' => $this->material_object_id,
        ]);

        return $dataProvider;
    }
}
