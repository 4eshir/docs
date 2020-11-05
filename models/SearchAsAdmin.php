<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\AsAdmin;

/**
 * SearchAsAdmin represents the model behind the search form of `app\models\common\AsAdmin`.
 */
class SearchAsAdmin extends AsAdmin
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'as_company_id', 'document_number', 'count', 'country_prod_id', 'version_id', 'license_id', 'register_id'], 'integer'],
            [['document_date', 'license_start', 'license_finish', 'comment', 'scan', 'as_name'], 'safe'],
            [['price'], 'number'],
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
        $query = AsAdmin::find();

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
            'as_company_id' => $this->as_company_id,
            'document_number' => $this->document_number,
            'document_date' => $this->document_date,
            'count' => $this->count,
            'price' => $this->price,
            'country_prod_id' => $this->country_prod_id,
            'license_start' => $this->license_start,
            'license_finish' => $this->license_finish,
            'version_id' => $this->version_id,
            'license_id' => $this->license_id,
            'register_id' => $this->register_id,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'scan', $this->scan]);

        return $dataProvider;
    }
}
