<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "group_project_themes".
 *
 * @property int $id
 * @property int $training_group_id
 * @property int $project_theme_id
 * @property int $project_type_id
 * @property int $confirm
 *
 * @property ProjectTheme $projectTheme
 * @property TrainingGroup $trainingGroup
 * @property ProjectType $projectType
 * @property TrainingGroupParticipant[] $trainingGroupParticipants
 */
class GroupProjectThemes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_project_themes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_id', 'project_theme_id', 'project_type_id'], 'required'],
            [['training_group_id', 'project_theme_id', 'project_type_id', 'confirm'], 'integer'],
            [['project_theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectTheme::className(), 'targetAttribute' => ['project_theme_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
            [['project_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectType::className(), 'targetAttribute' => ['project_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_group_id' => 'Training Group ID',
            'project_theme_id' => 'Project Theme ID',
            'project_type_id' => 'Project Type ID',
            'confirm' => 'Confirm',
        ];
    }

    /**
     * Gets query for [[ProjectTheme]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectTheme()
    {
        return $this->hasOne(ProjectTheme::className(), ['id' => 'project_theme_id']);
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(TrainingGroup::className(), ['id' => 'training_group_id']);
    }

    /**
     * Gets query for [[ProjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectType()
    {
        return $this->hasOne(ProjectType::className(), ['id' => 'project_type_id']);
    }

    /**
     * Gets query for [[TrainingGroupParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipants()
    {
        return $this->hasMany(TrainingGroupParticipant::className(), ['group_project_themes_id' => 'id']);
    }
}
