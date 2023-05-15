<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\MaterialObject;
use app\models\work\MaterialObjectWork;

/**
 * SearchMaterialObject represents the model behind the search form of `app\models\common\MaterialObject`.
 */
class SearchMaterialObject extends MaterialObjectWork
{
    public $nameLink;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'count', 'finance_source_id', 'type', 'is_education', 'state', 'status', 'write_off', 'expiration_date'], 'integer'],
            [['name', 'photo_local', 'photo_cloud', 'attribute', 'inventory_number', 'damage', 'lifetime', 'create_date'], 'safe'],
            [['price'], 'number'],
            [['nameLink'], 'string'],
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
        $query = MaterialObjectWork::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['nameLink'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
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
            'count' => $this->count,
            'price' => $this->price,
            'finance_source_id' => $this->finance_source_id,
            'type' => $this->type,
            'is_education' => $this->is_education,
            'state' => $this->state,
            'status' => $this->status,
            'write_off' => $this->write_off,
            'lifetime' => $this->lifetime,
            'expiration_date' => $this->expiration_date,
            'create_date' => $this->create_date,
        ]);

        $query->andFilterWhere(['like', 'name', $this->nameLink])
            ->andFilterWhere(['like', 'photo_local', $this->photo_local])
            ->andFilterWhere(['like', 'photo_cloud', $this->photo_cloud])
            ->andFilterWhere(['like', 'attribute', $this->attribute])
            ->andFilterWhere(['like', 'inventory_number', $this->inventory_number])
            ->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'damage', $this->damage]);

        return $dataProvider;
    }
}
