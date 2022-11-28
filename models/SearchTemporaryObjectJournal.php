<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\TemporaryObjectJournal;
use app\models\work\TemporaryObjectJournalWork;

/**
 * SearchTemporaryObjectJournal represents the model behind the search form of `app\models\common\TemporaryObjectJournal`.
 */
class SearchTemporaryObjectJournal extends TemporaryObjectJournal
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_give_id', 'user_get_id', 'confirm_give', 'confirm_get', 'material_object_id', 'container_id'], 'integer'],
            [['comment', 'date_give', 'date_get', 'real_date_get'], 'safe'],
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
        $query = TemporaryObjectJournalWork::find();

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
            'user_give_id' => $this->user_give_id,
            'user_get_id' => $this->user_get_id,
            'confirm_give' => $this->confirm_give,
            'confirm_get' => $this->confirm_get,
            'material_object_id' => $this->material_object_id,
            'container_id' => $this->container_id,
            'date_give' => $this->date_give,
            'date_get' => $this->date_get,
            'real_date_get' => $this->real_date_get,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
