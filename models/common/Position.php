<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "position".
 *
 * @property int $id
 * @property string $name
 *
 * @property Destination[] $destinations
 */
class Position extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'position';
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
     * Gets query for [[Destinations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDestinations()
    {
        return $this->hasMany(Destination::className(), ['position_id' => 'id']);
    }

    public function checkForeignKeys()
    {
        $doc_out = DocumentOut::find()->where(['position_id' => $this->id])->all();
        $doc_in = DocumentIn::find()->where(['position_id' => $this->id])->all();
        if (count($doc_out) > 0 || count($doc_in) > 0)
        {

            Yii::$app->session->addFlash('error', 'Невозможно удалить должность! Должность используется в документах');
            return false;
        }
        return true;
    }
}
