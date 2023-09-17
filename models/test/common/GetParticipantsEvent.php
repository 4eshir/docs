<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_participants_event".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $event_type_id
 * @property int|null $event_form_id
 * @property int|null $event_level_id
 * @property string|null $finish_date
 */
class GetParticipantsEvent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_participants_event';
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
            [['event_type_id', 'event_form_id', 'event_level_id'], 'integer'],
            [['finish_date'], 'safe'],
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
            'event_type_id' => 'Event Type ID',
            'event_form_id' => 'Event Form ID',
            'event_level_id' => 'Event Level ID',
            'finish_date' => 'Finish Date',
        ];
    }
}
