<?php

namespace app\models;

use app\models\common\Company;
use app\models\common\Destination;
use app\models\common\People;
use app\models\common\SendMethod;
use app\models\extended\DocumentOutExtended;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\DocumentOut;

/**
 * SearchDocumentOut represents the model behind the search form of `app\models\common\DocumentOut`.
 */
class SearchDocumentOut extends DocumentOut
{

    public $signedName;
    public $executorName;
    public $registerName;
    public $sendMethodName;
    public $companyName;
    public $positionName;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'position_id', 'signed_id', 'executor_id', 'send_method_id', 'register_id', 'document_number'], 'integer'],
            [['document_name', 'document_date', 'document_theme', 'sent_date', 'Scan', 'signedName', 'document_date',
                'executorName', 'registerName', 'sendMethodName', 'companyName', 'positionName'], 'safe'],
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
        $query = DocumentOut::find();
        $query->joinWith(['signed']);
        $query->joinWith(['executor']);
        $query->joinWith(['register']);
        $query->joinWith(['sendMethod']);
        $query->joinWith(['company']);
        $query->joinWith(['position']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['signedName'] = [
            'asc' => [People::tableName().'.secondname' => SORT_ASC],
            'desc' => [People::tableName().'.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['executorName'] = [
            'asc' => [People::tableName().'.secondname' => SORT_ASC],
            'desc' => [People::tableName().'.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['registerName'] = [
            'asc' => [People::tableName().'.secondname' => SORT_ASC],
            'desc' => [People::tableName().'.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sendMethodName'] = [
            'asc' => [SendMethod::tableName().'.name' => SORT_ASC],
            'desc' => [SendMethod::tableName().'.name' => SORT_DESC],
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
            'document_date' => $this->document_date,
            'document_name' => $this->document_name,
            'company_id' => $this->company_id,
            'signed_id' => $this->signed_id,
            'executor_id' => $this->executor_id,
            'send_method_id' => $this->send_method_id,
            'sent_date' => $this->sent_date,
            'register_id' => $this->register_id,
        ]);

        $query->andFilterWhere(['like', 'document_theme', $this->document_theme])
            ->andFilterWhere(['like', 'Scan', $this->Scan])
            ->andFilterWhere(['like', People::tableName().'.secondname', $this->signedName])
            ->andFilterWhere(['like', People::tableName().'.secondname', $this->executorName])
            ->andFilterWhere(['like', People::tableName().'.secondname', $this->registerName])
            ->andFilterWhere(['like', SendMethod::tableName().'.name', $this->sendMethodName])
            ->andFilterWhere(['like', Company::tableName().'.name', $this->companyName]);

        return $dataProvider;
    }
}
