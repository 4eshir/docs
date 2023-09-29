<?php

namespace app\models;

use app\models\work\CompanyWork;
use app\models\work\DestinationWork;
use app\models\work\PeopleWork;
use app\models\work\PositionWork;
use app\models\work\SendMethodWork;
use app\models\work\UserWork;
use app\models\extended\DocumentOutExtended;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\DocumentOutWork;
use yii\db\Query;
use Yii;

/**
 * SearchDocumentOut represents the model behind the search form of `app\models\common\DocumentOut`.
 */
class SearchDocumentOut extends DocumentOutWork
{

    public $signedName;
    public $executorName;
    public $creatorName;
    public $sendMethodName;
    public $positionCompany;
    public $start_date_search;
    public $finish_date_search;

    public $archive;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'position_id', 'signed_id', 'executor_id', 'send_method_id', 'creator_id', 'document_number', 'archive'], 'integer'],
            [['document_name', 'document_date', 'document_theme', 'sent_date', 'Scan', 'signedName', 'document_date',
                'executorName', 'creatorName', 'sendMethodName', 'positionCompany', 'document_number', 'key_words', 'isAnswer',
                'start_date_search', 'finish_date_search'], 'safe'],
        ];
    }

    function __construct($archive = null)
    {
        //parent::__construct($config);
        $this->archive = $archive;
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
        $session = Yii::$app->session;
        $tempArchive = $session->get("archiveOut");

        $query = DocumentOutWork::find();
        $query->joinWith(['signed signed', 'executor executor']);
        $query->joinWith(['creator']);
        $query->joinWith(['sendMethod']);
        $query->joinWith(['company']);
        $query->joinWith(['position']);

        if ($tempArchive === null)
        {
            $query = $query->where(['>', 'document_date', date("Y").'.01.01']);
            //var_dump($query->createCommand()->getRawSql());
        }

        if (array_key_exists("SearchDocumentOut", $params))
        {
            if (strlen($params["SearchDocumentOut"]["start_date_search"]) > 9 && strlen($params["SearchDocumentOut"]["finish_date_search"]) > 9)
            {

                $query = $tempArchive === null ?
                    $query->andWhere(['>=', 'document_date', $params["SearchDocumentOut"]["start_date_search"]])->andWhere(['<=', 'document_date', $params["SearchDocumentOut"]["finish_date_search"]])
                    : $query->where(['>=', 'document_date', $params["SearchDocumentOut"]["start_date_search"]])->andWhere(['<=', 'document_date', $params["SearchDocumentOut"]["finish_date_search"]]);
            }
            else if (strlen($params["SearchDocumentOut"]["start_date_search"]) > 9 && strlen($params["SearchDocumentOut"]["finish_date_search"]) < 9)
            {
                $query = $tempArchive === null ?
                    $query = $query->andWhere(['>=', 'document_date', $params["SearchDocumentOut"]["start_date_search"]])
                    :$query = $query->where(['>=', 'document_date', $params["SearchDocumentOut"]["start_date_search"]]);
            }
            else if (strlen($params["SearchDocumentOut"]["start_date_search"]) < 9 && strlen($params["SearchDocumentOut"]["finish_date_search"]) > 9)
            {
                $query = $tempArchive === null ?
                    $query->andWhere(['<=', 'document_date', $params["SearchDocumentOut"]["finish_date_search"]])
                    : $query->where(['<=', 'document_date', $params["SearchDocumentOut"]["finish_date_search"]]);
            }
        }



        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['document_date' => SORT_DESC, 'document_number' => SORT_DESC, 'document_postfix' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['isAnswer'] = [
            'asc' => ['isAnswer' => SORT_ASC],
            'desc' => ['isAnswer' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['signedName'] = [
            'asc' => ['signed.secondname' => SORT_ASC],
            'desc' => ['signed.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['executorName'] = [
            'asc' => ['executor.secondname' => SORT_ASC],
            'desc' => ['executor.secondname' => SORT_DESC],
        ];
            
        $dataProvider->sort->attributes['creatorName'] = [
            'asc' => ['creator.secondname' => SORT_ASC],
            'desc' => ['creator.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sendMethodName'] = [
            'asc' => [SendMethodWork::tableName().'.name' => SORT_ASC],
            'desc' => [SendMethodWork::tableName().'.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['creatorName'] = [
            'asc' => [UserWork::tableName().'.secondname' => SORT_ASC],
            'desc' => [UserWork::tableName().'.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['positionCompany'] = [
            'asc' => ['position.name' => SORT_ASC, 'company.name' => SORT_ASC],
            'desc' => ['position.name' => SORT_DESC, 'company.name' => SORT_DESC],

        ];




        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'document_number' => $this->document_number,
            'document_date' => $this->document_date,
            'document_name' => $this->document_name,
            'company_id' => $this->company_id,
            'signed_id' => $this->signed_id,
            'executor_id' => $this->executor_id,

            'send_method_id' => $this->send_method_id,
            'sent_date' => $this->sent_date,
            'creator_id' => $this->creator_id,
        ]);

        $query->andFilterWhere(['like', 'document_theme', $this->document_theme])
            ->andFilterWhere(['like', 'Scan', $this->Scan])
            ->andFilterWhere(['like', 'key_words', $this->key_words])
            ->andFilterWhere(['like', 'signed.secondname', $this->signedName])
            ->andFilterWhere(['like', 'executor.secondname', $this->executorName])
            ->andFilterWhere(['like', UserWork::tableName().'.secondname', $this->creatorName])
            ->andFilterWhere(['like', SendMethodWork::tableName().'.name', $this->sendMethodName])
            ->andFilterWhere(['like', 'position.name', $this->positionCompany])
            ->orFilterWhere(['like', 'company.name', $this->positionCompany]);

        return $dataProvider;
    }
}
