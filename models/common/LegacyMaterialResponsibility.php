<?php

namespace app\models\common;

use app\models\work\PeopleWork;
use Yii;

/**
 * This is the model class for table "legacy_material_responsibility".
 *
 * @property int $id
 * @property int $people_out_id
 * @property int $people_in_id
 * @property int $material_object_id
 * @property string $date
 *
 * @property MaterialObject $materialObject
 * @property People $peopleIn
 * @property People $peopleOut
 */
class LegacyMaterialResponsibility extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legacy_material_responsibility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_out_id', 'people_in_id', 'material_object_id', 'date'], 'required'],
            [['people_out_id', 'people_in_id', 'material_object_id'], 'integer'],
            [['date'], 'safe'],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
            [['people_in_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_in_id' => 'id']],
            [['people_out_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_out_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'people_out_id' => 'People Out ID',
            'people_in_id' => 'People In ID',
            'material_object_id' => 'Material Object ID',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[MaterialObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObject()
    {
        return $this->hasOne(MaterialObject::className(), ['id' => 'material_object_id']);
    }

    /**
     * Gets query for [[PeopleIn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleIn()
    {
        return $this->hasOne(People::className(), ['id' => 'people_in_id']);
    }

    /**
     * Gets query for [[PeopleOut]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleOut()
    {
        return $this->hasOne(People::className(), ['id' => 'people_out_id']);
    }
}
