<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\common\Certificat;
use app\models\work\CertificatWork;

/**
 * SearchCertificat represents the model behind the search form of `app\models\common\Certificat`.
 */
class SearchCertificat extends CertificatWork
{
    public $participantName;
    public $participantGroup;
    public $certificatView;
    public $certificatTemplateName;
    public $participantProtection;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'certificat_number', 'certificat_template_id', 'training_group_participant_id'], 'integer'],
            [['participantName', 'participantGroup', 'certificatView', 'certificatTemplateName'], 'string'],
            [['participantProtection'], 'safe'],
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
        $query = CertificatWork::find()->joinWith(['trainingGroupParticipant trainingGroupParticipant'])->joinWith(['trainingGroupParticipant.participant participant'])->joinWith(['trainingGroupParticipant.trainingGroup group'])->joinWith(['certificatTemplate template']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['participantName'] = [
            'asc' => ['participant.secondname' => SORT_ASC],
            'desc' => ['participant.secondname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['participantGroup'] = [
            'asc' => ['group.number' => SORT_ASC],
            'desc' => ['group.number' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['participantProtection'] = [
            'asc' => ['group.protection_date' => SORT_ASC],
            'desc' => ['group.protection_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['certificatView'] = [
            'asc' => ['certificat_number' => SORT_ASC],
            'desc' => ['certificat_number' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['certificatTemplateName'] = [
            'asc' => ['template.name' => SORT_ASC],
            'desc' => ['template.name' => SORT_DESC],
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
            'certificat_number' => $this->certificat_number,
            'certificat_template_id' => $this->certificat_template_id,
            'training_group_participant_id' => $this->training_group_participant_id,
            'protection_date' => $this->participantProtection,
        ])
        ->andFilterWhere(['like', 'group.number', $this->participantGroup])
        ->andFilterWhere(['like', 'certificat.certificat_number', $this->certificatView])
        ->andFilterWhere(['like', 'template.name', $this->certificatTemplateName])
        ->andFilterWhere(['like', 'CONCAT(participant.secondname, " ", participant.firstname, " ", participant.patronymic)', $this->participantName]);

        return $dataProvider;
    }
}
