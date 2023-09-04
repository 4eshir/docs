<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "use_years".
 *
 * @property int $id
 * @property string $start_date
 * @property string $end_date
 * @property int $as_admin_id
 *
 * @property AsAdmin $asAdmin
 */
class UseYears extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'use_years';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_date', 'end_date', 'as_admin_id'], 'required'],
            [['start_date', 'end_date'], 'safe'],
            [['as_admin_id'], 'integer'],
            [['as_admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsAdmin::className(), 'targetAttribute' => ['as_admin_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'as_admin_id' => 'As Admin ID',
        ];
    }

    /**
     * Gets query for [[AsAdmin]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsAdmin()
    {
        return $this->hasOne(AsAdmin::className(), ['id' => 'as_admin_id']);
    }
}
