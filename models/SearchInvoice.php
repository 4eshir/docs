<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\InvoiceWork;
use app\models\work\CompanyWorkWork;

/**
 * SearchInvoice represents the model behind the search form of `app\models\common\Invoice`.
 */
class SearchInvoice extends InvoiceWork
{
    /**
     * {@inheritdoc}
     */
    public $contractString;


    public function rules()
    {
        return [
            [['id', 'contractor_id', 'type'], 'integer'],
            [['number', 'date_product', 'date_invoice', 'document'], 'safe'],
            [['contractString'], 'string'],
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


        $query = InvoiceWork::find();
        $query->joinWith(['company company']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['contractString'] = [
            'asc' => ['company.name' => SORT_ASC],
            'desc' => ['company.name' => SORT_DESC],
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
            'contractor_id' => $this->contractor_id,
            'date_product' => $this->date_product,
            'date_invoice' => $this->date_invoice,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'document', $this->document])
            ->andFilterWhere(['like', 'company.name', $this->contractString]);

        return $dataProvider;
    }
}
