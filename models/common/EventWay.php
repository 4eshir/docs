<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "event_way".
 *
 * @property int $id
 * @property string $name
 *
 * @property ForeignEvent[] $foreignEvents
 */
class EventWay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_way';
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
     * Gets query for [[ForeignEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvents()
    {
        return $this->hasMany(ForeignEvent::className(), ['event_way_id' => 'id']);
    }
}
