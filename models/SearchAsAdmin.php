<?php

namespace app\models;

use app\models\common\Company;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\AsAdmin;

/**
 * SearchAsAdmin represents the model behind the search form of `app\models\common\AsAdmin`.
 */
class SearchAsAdmin extends AsAdmin
{
    public $copyrightName;
    public $countryName;
    public $requisitsName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'as_company_id', 'document_number', 'count', 'country_prod_id', 'license_id', 'register_id', 'distribution_type_id'], 'integer'],
            [['document_date', 'comment', 'scan', 'as_name', 'copyrightName', 'requisitsName', 'countryName', 'license'], 'safe'],
            [['copyrightName', 'requisitsName', 'countryName', 'unifed_register_number'], 'string'],
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
        $query->joinWith(['copyright copyright']);
        $query->joinWith(['asCompany asCompany']);
        $query->joinWith(['countryProd countryProd']);

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

        $dataProvider->sort->attributes['copyrightName'] = [
            'asc' => ['copyright.name' => SORT_ASC],
            'desc' => ['copyright.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['countryName'] = [
            'asc' => ['countryProd.name' => SORT_ASC],
            'desc' => ['countryProd.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['requisitsName'] = [
            'asc' => ['requisits' => SORT_ASC],
            'desc' => ['requisits' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['distribution_type_id'] = [
            'asc' => ['distribution_type_id' => SORT_ASC],
            'desc' => ['distribution_type_id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['license'] = [
            'asc' => ['license_id' => SORT_ASC],
            'desc' => ['license_id' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'copyright_id' => $this->copyright_id,
            'as_company_id' => $this->as_company_id,
            'document_number' => $this->document_number,
            'document_date' => $this->document_date,
            'count' => $this->count,
            'price' => $this->price,
            'country_prod_id' => $this->country_prod_id,
            'license_id' => $this->license_id,
            'register_id' => $this->register_id,
            'unifed_register_number' => $this->unifed_register_number,
            'distribution_type_id' => $this->distribution_type_id,
            'license' => $this->license_id,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'scan', $this->scan])
            ->andFilterWhere(['like', 'copyright.name', $this->copyrightName])
            ->andFilterWhere(['like', 'document_number', $this->requisitsName])
            ->andFilterWhere(['like', 'countryProd.name', $this->countryName])
            ->andFilterWhere(['like', 'unifed_register_number', $this->unifed_register_number])
            ->andFilterWhere(['like', 'license.name', $this->license])
            ->orFilterWhere(['like', 'document_date', $this->requisitsName])
            ->orFilterWhere(['like', 'asCompany.name', $this->requisitsName]);

        return $dataProvider;
    }
}
