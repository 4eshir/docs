<?php

namespace app\models\common;

use app\models\work\PeopleWork;
use Mpdf\Tag\P;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "people_material_object".
 *
 * @property int $id
 * @property int $people_id
 * @property int $material_object_id
 * @property string $acceptance_date
 *
 * @property MaterialObject $materialObject
 * @property People $people
 */
class PeopleMaterialObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'people_material_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_id', 'material_object_id'], 'required'],
            [['people_id', 'material_object_id'], 'integer'],
            [['acceptance_date'], 'string'],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'people_id' => 'Ответственный',
            'peopleName' => 'Ответственный',
            'material_object_id' => 'Объект',
            'materialObjectName' => 'Объект',
            'acceptance_date' => 'Дата',
            'history' => 'История',
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
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }
}
