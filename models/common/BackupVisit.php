<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "backup_visit".
 *
 * @property int $id
 * @property int|null $foreign_event_participant_id
 * @property int|null $training_group_lesson_id
 * @property int|null $status
 * @property string|null $structure
 */
class BackupVisit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'backup_visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foreign_event_participant_id', 'training_group_lesson_id', 'status'], 'integer'],
            [['structure'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'foreign_event_participant_id' => 'Foreign Event Participant ID',
            'training_group_lesson_id' => 'Training Group Lesson ID',
            'status' => 'Status',
            'structure' => 'Structure',
        ];
    }
}
