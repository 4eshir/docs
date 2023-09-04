<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_foreign_event_participant".
 *
 * @property int $id
 * @property string|null $birthdate
 *
 * @property GetGroupParticipantsTrainingGroupParticipant[] $getGroupParticipantsTrainingGroupParticipants
 */
class GetGroupParticipantsForeignEventParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_foreign_event_participant';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_report_test');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['birthdate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'birthdate' => 'Birthdate',
        ];
    }

    /**
     * Gets query for [[GetGroupParticipantsTrainingGroupParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetGroupParticipantsTrainingGroupParticipants()
    {
        return $this->hasMany(GetGroupParticipantsTrainingGroupParticipant::className(), ['participant_id' => 'id']);
    }
}
