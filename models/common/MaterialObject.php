<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "material_object".
 *
 * @property int $id
 * @property string $unique_id
 * @property int $material_object_type_id
 * @property string $name
 * @property string $acceptance_date
 * @property float $balance_price
 * @property int $count
 * @property int $main
 * @property string|null $files
 *
 * @property MaterialObjectType $materialObjectType
 * @property PeopleMaterialObject[] $peopleMaterialObjects
 * @property TemporaryJournal[] $temporaryJournals
 */
class MaterialObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'material_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['unique_id', 'material_object_type_id', 'name', 'acceptance_date', 'balance_price', 'count', 'main'], 'required'],
            [['material_object_type_id', 'count', 'main'], 'integer'],
            [['acceptance_date'], 'safe'],
            [['balance_price'], 'number'],
            [['unique_id', 'name', 'files'], 'string', 'max' => 1000],
            [['material_object_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObjectType::className(), 'targetAttribute' => ['material_object_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'unique_id' => 'Unique ID',
            'material_object_type_id' => 'Material Object Type ID',
            'name' => 'Name',
            'acceptance_date' => 'Acceptance Date',
            'balance_price' => 'Balance Price',
            'count' => 'Count',
            'main' => 'Main',
            'files' => 'Files',
        ];
    }

    /**
     * Gets query for [[MaterialObjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObjectType()
    {
        return $this->hasOne(MaterialObjectType::className(), ['id' => 'material_object_type_id']);
    }

    /**
     * Gets query for [[PeopleMaterialObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleMaterialObjects()
    {
        return $this->hasMany(PeopleMaterialObject::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[TemporaryJournals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemporaryJournals()
    {
        return $this->hasMany(TemporaryJournal::className(), ['material_object_id' => 'id']);
    }
}
