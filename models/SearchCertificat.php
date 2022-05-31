<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\Certificat;

/**
 * SearchCertificat represents the model behind the search form of `app\models\common\Certificat`.
 */
class SearchCertificat extends Certificat
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'certificat_number', 'certificat_template_id', 'training_group_participant_id'], 'integer'],
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
        $query = Certificat::find();

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
            'certificat_number' => $this->certificat_number,
            'certificat_template_id' => $this->certificat_template_id,
            'training_group_participant_id' => $this->training_group_participant_id,
        ]);

        return $dataProvider;
    }
}
