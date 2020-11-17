<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\DocumentIn;

/**
 * SearchDocumentIn represents the model behind the search form of `app\models\common\DocumentIn`.
 */
class SearchDocumentIn extends DocumentIn
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'local_number', 'real_number', 'position_id', 'company_id', 'signed_id', 'get_id', 'register_id'], 'integer'],
            [['local_date', 'real_date', 'document_theme', 'target', 'scan', 'applications', 'key_words'], 'safe'],
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
        $query = DocumentIn::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC]]
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
            'local_number' => $this->local_number,
            'local_date' => $this->local_date,
            'real_number' => $this->real_number,
            'real_date' => $this->real_date,
            'position_id' => $this->position_id,
            'company_id' => $this->company_id,
            'signed_id' => $this->signed_id,
            'get_id' => $this->get_id,
            'register_id' => $this->register_id,
        ]);

        $query->andFilterWhere(['like', 'document_theme', $this->document_theme])
            ->andFilterWhere(['like', 'target', $this->target])
            ->andFilterWhere(['like', 'scan', $this->scan])
            ->andFilterWhere(['like', 'applications', $this->applications])
            ->andFilterWhere(['like', 'key_words', $this->key_words]);

        return $dataProvider;
    }
}
