<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_training_group_lesson".
 *
 * @property int $id
 * @property string|null $lesson_date
 * @property int|null $training_group_id
 *
 * @property GetGroupParticipantsTrainingGroup $trainingGroup
 * @property GetGroupParticipantsVisit[] $getGroupParticipantsVisits
 */
class GetGroupParticipantsTrainingGroupLesson extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_training_group_lesson';
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
            [['lesson_date'], 'safe'],
            [['training_group_id'], 'integer'],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsTrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lesson_date' => 'Lesson Date',
            'training_group_id' => 'Training Group ID',
        ];
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(GetGroupParticipantsTrainingGroup::className(), ['id' => 'training_group_id']);
    }

    /**
     * Gets query for [[GetGroupParticipantsVisits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetGroupParticipantsVisits()
    {
        return $this->hasMany(GetGroupParticipantsVisit::className(), ['training_group_lesson_id' => 'id']);
    }
}
