<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\ForeignEventParticipants;

/**
 * SearchForeignEventParticipants represents the model behind the search form of `app\models\common\ForeignEventParticipants`.
 */
class SearchForeignEventParticipants extends ForeignEventParticipants
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
        $query = ForeignEventParticipants::find();
        if ($sort == 1)
        {
            $query = ForeignEventParticipants::find()->where(['is_true' => 0])->andWhere(['is', 'guaranted_true', new \yii\db\Expression('null')])->andWhere(['guaranted_true' => 0])->orWhere(['sex' => 'Другое']);
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
