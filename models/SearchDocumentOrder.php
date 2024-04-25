<?php

namespace app\models;

use app\models\work\PeopleWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\DocumentOrderWork;

/**
 * SearchDocumentOrder represents the model behind the search form of `app\models\common\DocumentOrder`.
 */
class SearchDocumentOrder extends DocumentOrderWork
{
    public $signedName;
    public $executorName;
    public $creatorName;
    public $bringName;
    public $stateName;
    public $branchString;

    public $documentNumberString;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_number', 'signed_id', 'bring_id', 'executor_id', 'scan', 'creator_id', 'branchString', 'nomenclature_id'], 'integer'],
            [['signedName', 'executorName', 'creatorName', 'bringName', 'stateName', 'documentNumberString'], 'string'],
            [['order_name', 'order_date', 'signedName', 'executorName', 'creatorName', 'bringName', 'stateName', 'key_words'], 'safe'],
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
    public function search($params, $sort)
    {
        $query = DocumentOrderWork::find();
        if ($sort == 1)
            $query = DocumentOrderWork::find()->where(['type' => 1])->orWhere(['type' => 10])->orWhere(['type' => 2]);  // основные, основно-архивые и по учету достижений
        else
            $query = DocumentOrderWork::find()->where(['type' => 0])->orWhere(['type' => 11]);  // учебные и учебно-архивные
        $query->joinWith(['signed signed', 'executor executor', /*'register register', */'bring bring']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_date' => SORT_DESC, 'order_copy_id' => SORT_DESC, 'order_postfix' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['signedName'] = [
            'asc' => ['signed.secondname' => SORT_ASC],
            'desc' => ['signed.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['executorName'] = [
            'asc' => ['executor.secondname' => SORT_ASC],
            'desc' => ['executor.secondname' => SORT_DESC],
        ];

        /*$dataProvider->sort->attributes['registerName'] = [
            'asc' => ['register.secondname' => SORT_ASC],
            'desc' => ['register.secondname' => SORT_DESC],
        ];*/

        $dataProvider->sort->attributes['bringName'] = [
            'asc' => ['bring.secondname' => SORT_ASC],
            'desc' => ['bring.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['stateName'] = [
            'asc' => ['state' => SORT_ASC],
            'desc' => ['state' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['documentNumberString'] = [
            'asc' => ['order_number' => SORT_ASC, 'order_copy_id' => SORT_ASC, 'order_postfix' => SORT_ASC],
            'desc' => ['order_number' => SORT_DESC, 'order_copy_id' => SORT_DESC, 'order_postfix' => SORT_DESC],
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
            'creator_id' => $this->creator_id,
            'state' => $this->state,
        ]);

        if ($this->documentNumberString != null)
        {
            $temp = explode("/", $this->documentNumberString);
            if (count($temp) == 1)
            {
                if (strripos($this->documentNumberString, '-'))
                    $this->order_number = $temp[0];
                else
                    $this->order_copy_id = $temp[0];
            }
            else
            {
                $this->order_number = $temp[0];
                $this->order_copy_id = $temp[1];
                if (count($temp) > 2) $this->order_postfix = $temp[2];
            }
        }

        $query->andFilterWhere(['like', 'order_name', $this->order_name])
            ->andFilterWhere(['like', 'signed.secondname', $this->signedName])
            ->andFilterWhere(['like', 'executor.secondname', $this->executorName])
            //->andFilterWhere(['like', 'register.secondname', $this->registerName])
            ->andFilterWhere(['like', 'bring.secondname', $this->bringName])
            ->andFilterWhere(['=', 'order_copy_id', $this->order_copy_id])
            ->andFilterWhere(['=', 'order_number', $this->order_number])
            ->andFilterWhere(['=', 'order_postfix', $this->order_postfix])
            ->andFilterWhere(['like', 'key_words', $this->key_words])
            ->andFilterWhere(['like', 'nomenclature_id', $this->nomenclature_id]);



        return $dataProvider;
    }
}
