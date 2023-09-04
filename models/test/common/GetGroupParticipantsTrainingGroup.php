<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_training_group".
 *
 * @property int $id
 * @property int|null $training_program_id
 *
 * @property GetGroupParticipantsTeacherGroup[] $getGroupParticipantsTeacherGroups
 * @property GetGroupParticipantsTrainingProgram $trainingProgram
 * @property GetGroupParticipantsTrainingGroupParticipant[] $getGroupParticipantsTrainingGroupParticipants
 */
class GetGroupParticipantsTrainingGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_training_group';
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
            [['training_program_id'], 'integer'],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsTrainingProgram::className(), 'targetAttribute' => ['training_program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_program_id' => 'Training Program ID',
        ];
    }

    /**
     * Gets query for [[GetGroupParticipantsTeacherGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetGroupParticipantsTeacherGroups()
    {
        return $this->hasMany(GetGroupParticipantsTeacherGroup::className(), ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingProgram]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingProgram()
    {
        return $this->hasOne(GetGroupParticipantsTrainingProgram::className(), ['id' => 'training_program_id']);
    }

    /**
     * Gets query for [[GetGroupParticipantsTrainingGroupParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetGroupParticipantsTrainingGroupParticipants()
    {
        return $this->hasMany(GetGroupParticipantsTrainingGroupParticipant::className(), ['training_group_id' => 'id']);
    }
}
