<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "auditorium".
 *
 * @property int $id
 * @property string $name
 * @property int $branch_id
 *
 * @property Branch $branch
 */
class Auditorium extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditorium';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название аудитории',
            'branch_id' => 'Branch ID',
        ];
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    public function getFullName()
    {
        return $this->name.' ('.$this->branch->name.')';
    }
}
