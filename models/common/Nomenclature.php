<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "nomenclature".
 *
 * @property int $id
 * @property string|null $number
 * @property string|null $name
 * @property int $branch_id
 * @property int $actuality
 * @property int|null $type
 *
 * @property Branch $branch
 */
class Nomenclature extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nomenclature';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['branch_id', 'actuality'], 'required'],
            [['branch_id', 'actuality', 'type'], 'integer'],
            [['number'], 'string', 'max' => 5],
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
            'number' => 'Number',
            'name' => 'Name',
            'branch_id' => 'Branch ID',
            'actuality' => 'Actuality',
            'type' => 'Type',
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
}
