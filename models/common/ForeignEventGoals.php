<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "foreign_event_goals".
 *
 * @property int $id
 * @property string $name
 *
 * @property DocumentOrderSupplement[] $documentOrderSupplements
 */
class ForeignEventGoals extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foreign_event_goals';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
        ];
    }

    /**
     * Gets query for [[DocumentOrderSupplements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOrderSupplements()
    {
        return $this->hasMany(DocumentOrderSupplement::className(), ['foreign_event_goals_id' => 'id']);
    }
}
