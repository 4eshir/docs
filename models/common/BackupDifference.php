<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "backup_difference".
 *
 * @property int $id
 * @property int $visit_id
 * @property int $old_status
 * @property int $new_status
 * @property string $date
 *
 * @property Visit $visit
 */
class BackupDifference extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'backup_difference';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['visit_id', 'old_status', 'new_status', 'date'], 'required'],
            [['visit_id', 'old_status', 'new_status'], 'integer'],
            [['date'], 'safe'],
            [['visit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Visit::className(), 'targetAttribute' => ['visit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'visit_id' => 'Visit ID',
            'old_status' => 'Old Status',
            'new_status' => 'New Status',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[Visit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisit()
    {
        return $this->hasOne(Visit::className(), ['id' => 'visit_id']);
    }
}
