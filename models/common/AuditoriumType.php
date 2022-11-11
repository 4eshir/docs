<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "auditorium_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property Auditorium[] $auditoria
 */
class AuditoriumType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditorium_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 1000],
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
        ];
    }

    /**
     * Gets query for [[Auditoria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditoria()
    {
        return $this->hasMany(Auditorium::className(), ['auditorium_type_id' => 'id']);
    }
}
