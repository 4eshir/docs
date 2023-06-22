<?php

namespace app\models\temporary;

use Yii;

/**
 * This is the model class for table "test_db_object".
 *
 * @property int $id
 * @property string $tablename
 * @property int $object_id
 */
class TestDbObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_db_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tablename', 'object_id'], 'required'],
            [['object_id'], 'integer'],
            [['tablename'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tablename' => 'Tablename',
            'object_id' => 'Object ID',
        ];
    }
}
