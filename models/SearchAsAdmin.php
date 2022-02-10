<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\AsAdminWork;

/**
 * SearchAsAdmin represents the model behind the search form of `app\models\common\AsAdmin`.
 */
class SearchAsAdmin extends AsAdminWork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'copyright_id', 'as_company_id', 'count', 'country_prod_id', 'distribution_type_id', 'license_id', 'register_id', 'as_type_id', 'license_count', 'license_term_type_id', 'license_status'], 'integer'],
            [['as_name', 'document_number', 'document_date', 'unifed_register_number', 'comment', 'scan', 'license_file', 'commercial_offers', 'service_note', 'contract_subject'], 'safe'],
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
        $query = AsAdminWork::find();

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
            'copyright_id' => $this->copyright_id,
            'as_company_id' => $this->as_company_id,
            'document_date' => $this->document_date,
            'count' => $this->count,
            'price' => $this->price,
            'country_prod_id' => $this->country_prod_id,
            'distribution_type_id' => $this->distribution_type_id,
            'license_id' => $this->license_id,
            'register_id' => $this->register_id,
            'as_type_id' => $this->as_type_id,
            'license_count' => $this->license_count,
            'license_term_type_id' => $this->license_term_type_id,
            'license_status' => $this->license_status,
        ]);

        $query->andFilterWhere(['like', 'as_name', $this->as_name])
            ->andFilterWhere(['like', 'document_number', $this->document_number])
            ->andFilterWhere(['like', 'unifed_register_number', $this->unifed_register_number])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'scan', $this->scan])
            ->andFilterWhere(['like', 'license_file', $this->license_file])
            ->andFilterWhere(['like', 'commercial_offers', $this->commercial_offers])
            ->andFilterWhere(['like', 'service_note', $this->service_note])
            ->andFilterWhere(['like', 'contract_subject', $this->contract_subject]);

        return $dataProvider;
    }
}
