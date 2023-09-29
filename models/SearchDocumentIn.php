<?php

namespace app\models;

use app\models\work\InOutDocsWork;
use app\models\work\SendMethodWork;
use phpDocumentor\Reflection\Types\Object_;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\DocumentInWork;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * SearchDocumentIn represents the model behind the search form of `app\models\common\DocumentIn`.
 */
class SearchDocumentIn extends DocumentInWork
{
    public $correspondentName;
    public $companyName;
    public $sendMethodName;
    public $start_date_search;
    public $finish_date_search;

    public $fullNumber;
    public $archive;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'local_number', 'position_id', 'company_id', 'signed_id', 'get_id', 'creator_id', 'archive'], 'integer'],
            [['real_number', 'fullNumber'], 'string'],
            [['local_date', 'real_date', 'document_theme', 'target', 'scan', 'applications', 'key_words', 'correspondentName', 'companyName', 'sendMethodName',
                'start_date_search', 'finish_date_search'], 'safe'],
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

    function __construct($archive = null)
    {
        //parent::__construct($config);
        $this->archive = $archive;
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
        $session = Yii::$app->session;
        $tempArchive = $session->get("archiveIn");

        $query = DocumentInWork::find();
        if ($sort !== null)
        {
            if ($sort == 1)
            {
                $subquery = InOutDocsWork::find()->where(['<', 'date', date('Y-m-d')])->andWhere(['document_out_id' => null])->all();
                $in = '';
                foreach ($subquery as $sq) {
                    $in .= $sq->document_in_id.',';
                }
                $in = substr($in,0,-1);
                if (strlen($in) !== 0)
                    $query = DocumentInWork::findBySql('SELECT * FROM `document_in` WHERE `id` IN ('.$in.')');
                else
                    $query = DocumentInWork::findBySql('SELECT * FROM `document_in` WHERE `id` = -1');
            }
            if ($sort == 2)
            {
                $subquery = InOutDocsWork::find()->where(['document_out_id' => null])->all();
                $in = '';
                foreach ($subquery as $sq) {
                    $in .= $sq->document_in_id.',';
                }
                $in = substr($in,0,-1);
                if (strlen($in) !== 0)
                    $query = DocumentInWork::findBySql('SELECT * FROM `document_in` WHERE `id` IN ('.$in.')');
                else
                    $query = DocumentInWork::findBySql('SELECT * FROM `document_in` WHERE `id` = -1');
            }
        }
        $query->joinWith(['correspondent correspondent']);
        $query->joinWith(['company']);
        $query->joinWith(['sendMethod']);

        if ($tempArchive === null)
        {
            $query = $query->where(['>', 'local_date', date("Y").'.01.01']);
            //var_dump($query->createCommand()->getRawSql());
        }

        if (array_key_exists("SearchDocumentIn", $params))
        {
            if (strlen($params["SearchDocumentIn"]["start_date_search"]) > 9 && strlen($params["SearchDocumentIn"]["finish_date_search"]) > 9)
            {
                $query = $tempArchive === null ?
                    $query->andWhere(['>=', 'real_date', $params["SearchDocumentIn"]["start_date_search"]])->andWhere(['<=', 'real_date', $params["SearchDocumentIn"]["finish_date_search"]])
                    : $query->where(['>=', 'real_date', $params["SearchDocumentIn"]["start_date_search"]])->andWhere(['<=', 'real_date', $params["SearchDocumentIn"]["finish_date_search"]]);
            }
            else if (strlen($params["SearchDocumentIn"]["start_date_search"]) > 9 && strlen($params["SearchDocumentIn"]["finish_date_search"]) < 9)
            {
                $query = $tempArchive === null ?
                    $query->andWhere(['>=', 'real_date', $params["SearchDocumentIn"]["start_date_search"]])
                    : $query->where(['>=', 'real_date', $params["SearchDocumentIn"]["start_date_search"]]);
            }
            else if (strlen($params["SearchDocumentIn"]["start_date_search"]) < 9 && strlen($params["SearchDocumentIn"]["finish_date_search"]) > 9)
            {
                $query = $tempArchive === null ?
                    $query->andWhere(['<=', 'real_date', $params["SearchDocumentIn"]["finish_date_search"]])
                    : $query->where(['<=', 'real_date', $params["SearchDocumentIn"]["finish_date_search"]]);
            }
        }




        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['local_date' => SORT_DESC, 'local_number' => SORT_DESC, 'local_postfix' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['needAnswer'] = [
            'asc' => ['needAnswer' => SORT_DESC],
            'desc' => ['needAnswer' => SORT_ASC],
        ];

        $dataProvider->sort->attributes['correspondentName'] = [
            'asc' => ['correspondent.secondname' => SORT_ASC],
            'desc' => ['correspondent.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['companyName'] = [
            'asc' => ['company.name' => SORT_ASC],
            'desc' => ['company.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sendMethodName'] = [
            'asc' => [SendMethodWork::tableName().'.name' => SORT_ASC],
            'desc' => [SendMethodWork::tableName().'.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['fullNumber'] = [
            'asc' => ['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC],
            'desc' => ['local_number' => SORT_DESC, 'local_postfix' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'document_in.id' => $this->id,
            'local_number' => $this->local_number,
            'local_date' => $this->local_date,
            'real_number' => $this->real_number,
            'real_date' => $this->real_date,
            'position_id' => $this->position_id,
            'company_id' => $this->company_id,
            'correspondent_id' => $this->correspondent_id,
            'signed_id' => $this->signed_id,
            'get_id' => $this->get_id,
            'creator_id' => $this->creator_id,
        ]);

        $query->andFilterWhere(['like', 'document_theme', $this->document_theme])
            ->andFilterWhere(['like', 'target', $this->target])
            ->andFilterWhere(['like', 'scan', $this->scan])
            ->andFilterWhere(['like', 'applications', $this->applications])
            ->andFilterWhere(['like', 'correspondent.secondname', $this->correspondentName])
            ->andFilterWhere(['like', 'company.name', $this->companyName])
            ->andFilterWhere(['like', SendMethodWork::tableName().'.name', $this->sendMethodName])
            ->andFilterWhere(['=', 'local_number', $this->fullNumber])
            ->orFilterWhere(['=', 'local_postfix', $this->fullNumber])
            ->andFilterWhere(['like', 'key_words', $this->key_words]);

        return $dataProvider;
    }
}
