<?php

namespace app\models\temporary;

use Yii;

/**
 * This is the model class for table "t_click".
 *
 * @property int $id
 * @property string $name
 * @property string|null $time
 */
class TClick extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 't_click';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['time'], 'safe'],
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
            'time' => 'Time',
        ];
    }
}
