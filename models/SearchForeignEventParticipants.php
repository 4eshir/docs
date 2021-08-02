<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\work\ForeignEventParticipantsWork;

/**
 * SearchForeignEventParticipants represents the model behind the search form of `app\models\common\ForeignEventParticipants`.
 */
class SearchForeignEventParticipants extends ForeignEventParticipantsWork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['firstname', 'secondname', 'patronymic'], 'string'],
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
        $query = ForeignEventParticipantsWork::find();
        if ($sort == 1)
        {
            $str = "SELECT * FROM `foreign_event_participants` WHERE `is_true` <> 1 AND (`guaranted_true` IS NULL OR `guaranted_true` = 0) ORDER BY `secondname`";
            $query = ForeignEventParticipantsWork::findBySql($str);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['secondname' => SORT_ASC, 'firstname' => SORT_ASC]]
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
            'firstname' => $this->firstname,
            'secondname' => $this->secondname,
            'patronymic' => $this->patronymic,
        ]);

        return $dataProvider;
    }
}
