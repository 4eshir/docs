<?php

namespace app\models;

use app\models\common\People;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\DocumentOrder;

/**
 * SearchDocumentOrder represents the model behind the search form of `app\models\common\DocumentOrder`.
 */
class SearchDocumentOrder extends DocumentOrder
{
    public $signedName;
    public $executorName;
    public $registerName;
    public $bringName;
    public $stateName;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_number', 'signed_id', 'bring_id', 'executor_id', 'scan', 'register_id'], 'integer'],
            [['signedName', 'executorName', 'registerName', 'bringName', 'stateName'], 'string'],
            [['order_name', 'order_date', 'signedName', 'executorName', 'registerName', 'bringName', 'stateName', 'key_words'], 'safe'],
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
        $query = DocumentOrder::find();
        $query->joinWith(['signed signed', 'executor executor', 'register register', 'bring bring']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_copy_id' => SORT_DESC, 'order_postfix' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['signedName'] = [
            'asc' => ['signed.secondname' => SORT_ASC],
            'desc' => ['signed.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['executorName'] = [
            'asc' => ['executor.secondname' => SORT_ASC],
            'desc' => ['executor.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['registerName'] = [
            'asc' => ['register.secondname' => SORT_ASC],
            'desc' => ['register.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['bringName'] = [
            'asc' => ['bring.secondname' => SORT_ASC],
            'desc' => ['bring.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['stateName'] = [
            'asc' => ['state' => SORT_ASC],
            'desc' => ['state' => SORT_DESC],
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
            'order_number' => $this->order_number,
            'order_date' => $this->order_date,
            'signed_id' => $this->signed_id,
            'bring_id' => $this->bring_id,
            'executor_id' => $this->executor_id,
            'scan' => $this->scan,
            'register_id' => $this->register_id,
            'state' => $this->state,
        ]);

        $query->andFilterWhere(['like', 'order_name', $this->order_name])
            ->andFilterWhere(['like', 'signed.secondname', $this->signedName])
            ->andFilterWhere(['like', 'executor.secondname', $this->executorName])
            ->andFilterWhere(['like', 'register.secondname', $this->registerName])
            ->andFilterWhere(['like', 'bring.secondname', $this->bringName])
            ->andFilterWhere(['like', 'key_words', $this->key_words]);


        return $dataProvider;
    }
}
