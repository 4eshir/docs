<?php

namespace app\models;

use app\models\work\EventBranchWork;
use app\models\work\TeacherParticipantWork;
use Yii;
use yii\base\BaseObject;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\ForeignEventWork;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * SearchForeignEvent represents the model behind the search form of `app\models\common\ForeignEvent`.
 */
class SearchForeignEvent extends ForeignEventWork
{
    public $companyString;
    public $eventLevelString;
    public $eventWayString;
    public $start_date_search;
    public $finish_date_search;
    public $secondnameParticipant;
    public $secondnameTeacher;
    public $nameBranch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'order_business_trip_id'], 'integer'],
            [['name', 'start_date', 'finish_date', 'city', 'key_words', 'docs_achievement', 'companyString', 'eventLevelString', 'eventWayString', 'start_date_search', 'finish_date_search', 'secondnameParticipant', 'secondnameTeacher', 'nameBranch'], 'safe'],
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
        $query = ForeignEventWork::find()->orderBy(['finish_date' => SORT_DESC, 'start_date' => SORT_DESC]);

        if (array_key_exists("SearchForeignEvent", $params))
        {
            if (strlen($params["SearchForeignEvent"]["secondnameParticipant"]) > 2)
            {
                $tps = TeacherParticipantWork::find()->joinWith(['participant participant'])->where(['LIKE', 'participant.secondname', $params["SearchForeignEvent"]["secondnameParticipant"]])->all();
                $tpIds = [];
                foreach ($tps as $tp) $tpIds[] = $tp->foreign_event_id;
                $query = $query->andWhere(['IN', 'foreign_event.id', $tpIds]);
            }
            if (strlen($params["SearchForeignEvent"]["start_date_search"]) > 9 && strlen($params["SearchForeignEvent"]["finish_date_search"]) > 9)
            {
                $query = $query->andWhere(['IN', 'foreign_event.id',
                    (new Query())->select('foreign_event.id')->from('foreign_event')->where(['>=', 'finish_date', $params["SearchForeignEvent"]["start_date_search"]])
                        ->andWhere(['<=', 'finish_date', $params["SearchForeignEvent"]["finish_date_search"]])]);

            }
            if (strlen($params["SearchForeignEvent"]["secondnameTeacher"]) > 1)
            {
                $tps = TeacherParticipantWork::find()->joinWith(['teacher teacher'])->where(['LIKE', 'teacher.secondname', $params["SearchForeignEvent"]["secondnameTeacher"]])->all();
                $tpIds = [];
                foreach ($tps as $tp) $tpIds[] = $tp->foreign_event_id;
                $query = $query->andWhere(['IN', 'foreign_event.id', $tpIds]);
            }
            if (strlen($params["SearchForeignEvent"]["nameBranch"]) > 0)
            {
                $branchs = TeacherParticipantWork::find()->joinWith(['teacherParticipantBranches teacherParticipantBranches'])->where(['teacherParticipantBranches.branch_id' => $params["SearchForeignEvent"]["nameBranch"]])->all();
                $bIds = [];
                foreach ($branchs as $branch) $bIds[] = $branch->foreign_event_id;
                $query = $query->andWhere(['IN', 'foreign_event.id', $bIds]);
            }
        }


        /*
        $qc = 1;
        if (strlen($params["SearchForeignEvent"]["secondnameParticipant"]) > 2)
        {
            $qc = $qc + 1;
            $strAddLeft = "SELECT * FROM (";
            $strAddRight = ") as t".$qc." WHERE `t".$qc."`.`participants` LIKE '%".$params["SearchForeignEvent"]["secondnameParticipant"]."%'";
            $str = $strAddLeft.$str.$strAddRight;
            $query = ForeignEventWork::findBySql($str);

        }
        if (strlen($params["SearchForeignEvent"]["start_date_search"]) > 9 && strlen($params["SearchForeignEvent"]["finish_date_search"]) > 9)
        {
            $qc = $qc + 1;
            $strAddLeft = "SELECT * FROM (";
            $strAddRight = ") as t".$qc." WHERE `t".$qc."`.`start_date` >= '".$params["SearchForeignEvent"]["start_date_search"]."' AND `t".$qc."`.`start_date` <= '".$params["SearchForeignEvent"]["finish_date_search"]."'";
            $str = $strAddLeft.$str.$strAddRight;
            $query = ForeignEventWork::findBySql($str);
        }
        if (strlen($params["SearchForeignEvent"]["secondnameTeacher"]) > 1)
        {
            $qc = $qc + 1;
            $strAddLeft = "SELECT * FROM (";
            $strAddRight = ") as t".$qc." WHERE `t".$qc."`.`teachers` LIKE '%".$params["SearchForeignEvent"]["secondnameTeacher"]."%'";
            $str = $strAddLeft.$str.$strAddRight;
            $query = ForeignEventWork::findBySql($str);
        }
        if (strlen($params["SearchForeignEvent"]["nameBranch"]) > 1)
        {
            $qc = $qc + 1;
            $strAddLeft = "SELECT * FROM (";
            $strAddRight = ") as t".$qc." WHERE `t".$qc."`.`branchs` LIKE '%".$params["SearchForeignEvent"]["nameBranch"]."%'";
            $str = $strAddLeft.$str.$strAddRight;
            $query = ForeignEventWork::findBySql($str);
        }*/



        $query->joinWith(['company company']);
        $query->joinWith(['eventLevel']);
        $query->joinWith(['eventWay']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['participants'] = [
            'asc' => ['foreignEventParticipants.Secondname' => SORT_ASC],
            'desc' => ['foreignEventParticipants.Secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['companyString'] = [
            'asc' => ['company.Name' => SORT_ASC],
            'desc' => ['company.Name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventLevelString'] = [
            'asc' => ['event_level.Name' => SORT_ASC],
            'desc' => ['event_level.Name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['eventWayString'] = [
            'asc' => ['event_way.Name' => SORT_ASC],
            'desc' => ['event_way.Name' => SORT_DESC],
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
            'company_id' => $this->company_id,
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'event_way_id' => $this->event_way_id,
            'event_level_id' => $this->event_level_id,
            'min_participants_age' => $this->min_participants_age,
            'max_participants_age' => $this->max_participants_age,
            'business_trip' => $this->business_trip,
            'escort_id' => $this->escort_id,
            'order_participation_id' => $this->order_participation_id,
            'order_business_trip_id' => $this->order_business_trip_id,
        ]);

        $query->andFilterWhere(['like', 'foreign_event.name', $this->name])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'key_words', $this->key_words])
            ->andFilterWhere(['like', 'company.name', $this->companyString])
            ->andFilterWhere(['like', 'event_level.name', $this->eventLevelString])
            ->andFilterWhere(['like', 'event_way.name', $this->eventWayString])
            ->andFilterWhere(['like', 'foreign_event_participants.secondname', $this->participants])
            ->andFilterWhere(['like', 'docs_achievement', $this->docs_achievement]);

        return $dataProvider;
    }
}
