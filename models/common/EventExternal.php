<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "event_external".
 *
 * @property int $id
 * @property string $name
 */
class EventExternal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_external';
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
            'name' => 'Назавние мероприятия',
        ];
    }
}
