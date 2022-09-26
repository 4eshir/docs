<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "container_object".
 *
 * @property int $id
 * @property int $container_id
 * @property int $material_object_id если объект не является контейнером, то создаем запись, иначе - нет
 *
 * @property Container $container
 * @property MaterialObject $materialObject
 */
class ContainerObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'container_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['container_id', 'material_object_id'], 'required'],
            [['container_id', 'material_object_id'], 'integer'],
            [['container_id'], 'exist', 'skipOnError' => true, 'targetClass' => Container::className(), 'targetAttribute' => ['container_id' => 'id']],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
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
            'material_object_id' => 'Material Object ID',
        ];
    }

    /**
     * Gets query for [[Container]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContainer()
    {
        return $this->hasOne(Container::className(), ['id' => 'container_id']);
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
}
