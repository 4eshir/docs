<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "container_errors".
 *
 * @property int $id
 * @property int $container_id
 * @property int $errors_id
 * @property string $time_start
 * @property string|null $time_the_end
 * @property int|null $critical
 * @property int|null $amnesty
 */
class ContainerErrors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'container_errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['container_id', 'errors_id', 'time_start'], 'required'],
            [['container_id', 'errors_id', 'critical', 'amnesty'], 'integer'],
            [['time_start', 'time_the_end'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'container_id' => 'Container ID',
            'errors_id' => 'Errors ID',
            'time_start' => 'Time Start',
            'time_the_end' => 'Time The End',
            'critical' => 'Critical',
            'amnesty' => 'Amnesty',
        ];
    }
}
