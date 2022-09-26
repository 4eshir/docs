<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "container".
 *
 * @property int $id
 * @property string $name
 * @property int|null $container_id некоторые контейнеры являются составляющими других контейнеров
 * @property int|null $material_object_id некоторые контейнеры являются материальными объектами
 * @property int|null $auditorium_id некоторые контейнеры - помещения
 *
 * @property Container $container
 * @property Container[] $containers
 * @property Auditorium $auditorium
 * @property MaterialObject $materialObject
 * @property ContainerObject[] $containerObjects
 * @property HistoryObject[] $historyObjects
 * @property TemporaryObjectJournal[] $temporaryObjectJournals
 */
class Container extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'container';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['container_id', 'material_object_id', 'auditorium_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['container_id'], 'exist', 'skipOnError' => true, 'targetClass' => Container::className(), 'targetAttribute' => ['container_id' => 'id']],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
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
            'name' => 'Name',
            'container_id' => 'Container ID',
            'material_object_id' => 'Material Object ID',
            'auditorium_id' => 'Auditorium ID',
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
     * Gets query for [[Containers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContainers()
    {
        return $this->hasMany(Container::className(), ['container_id' => 'id']);
    }

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::className(), ['id' => 'auditorium_id']);
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
     * Gets query for [[ContainerObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContainerObjects()
    {
        return $this->hasMany(ContainerObject::className(), ['container_id' => 'id']);
    }

    /**
     * Gets query for [[HistoryObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoryObjects()
    {
        return $this->hasMany(HistoryObject::className(), ['container_id' => 'id']);
    }

    /**
     * Gets query for [[TemporaryObjectJournals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemporaryObjectJournals()
    {
        return $this->hasMany(TemporaryObjectJournal::className(), ['container_id' => 'id']);
    }
}
