<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_participants_team_name".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $foreign_event_id
 *
 * @property GetParticipantAchievementsParticipantAchievement[] $getParticipantAchievementsParticipantAchievements
 * @property GetParticipantsTeam[] $getParticipantsTeams
 * @property GetParticipantsEvent $foreignEvent
 */
class GetParticipantsTeamName extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_participants_team_name';
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
            [['foreign_event_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetParticipantsEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'foreign_event_id' => 'Foreign Event ID',
        ];
    }

    /**
     * Gets query for [[GetParticipantAchievementsParticipantAchievements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetParticipantAchievementsParticipantAchievements()
    {
        return $this->hasMany(GetParticipantAchievementsParticipantAchievement::className(), ['team_name_id' => 'id']);
    }

    /**
     * Gets query for [[GetParticipantsTeams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetParticipantsTeams()
    {
        return $this->hasMany(GetParticipantsTeam::className(), ['team_name_id' => 'id']);
    }

    /**
     * Gets query for [[ForeignEvent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvent()
    {
        return $this->hasOne(GetParticipantsEvent::className(), ['id' => 'foreign_event_id']);
    }
}
