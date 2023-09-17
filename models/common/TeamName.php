<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "team_name".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $foreign_event_id
 *
 * @property Team[] $teams
 * @property ForeignEvent $foreignEvent
 */
class TeamName extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_name';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foreign_event_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
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
     * Gets query for [[Teams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['team_name_id' => 'id']);
    }

    /**
     * Gets query for [[ForeignEvent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvent()
    {
        return $this->hasOne(ForeignEvent::className(), ['id' => 'foreign_event_id']);
    }
}
