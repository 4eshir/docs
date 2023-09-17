<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_training_program".
 *
 * @property int $id
 * @property int|null $focus_id
 * @property int|null $allow_remote_id
 *
 * @property GetGroupParticipantsTrainingGroup[] $getGroupParticipantsTrainingGroups
 */
class GetGroupParticipantsTrainingProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_training_program';
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
            [['focus_id', 'allow_remote_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'focus_id' => 'Focus ID',
            'allow_remote_id' => 'Allow Remote ID',
        ];
    }

    /**
     * Gets query for [[GetGroupParticipantsTrainingGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetGroupParticipantsTrainingGroups()
    {
        return $this->hasMany(GetGroupParticipantsTrainingGroup::className(), ['training_program_id' => 'id']);
    }
}
