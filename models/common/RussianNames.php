<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "russian_names".
 *
 * @property int $ID
 * @property string $Name
 * @property string $Sex
 * @property int $PeoplesCount
 * @property string $WhenPeoplesCount
 * @property string $Source
 */
class RussianNames extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'russian_names';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'Sex', 'PeoplesCount', 'WhenPeoplesCount', 'Source'], 'required'],
            [['PeoplesCount'], 'integer'],
            [['WhenPeoplesCount'], 'safe'],
            [['Name', 'Source'], 'string', 'max' => 1000],
            [['Sex'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'Name' => 'Name',
            'Sex' => 'Sex',
            'PeoplesCount' => 'Peoples Count',
            'WhenPeoplesCount' => 'When Peoples Count',
            'Source' => 'Source',
        ];
    }
}
