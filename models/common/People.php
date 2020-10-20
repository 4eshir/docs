<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "people".
 *
 * @property int $id
 * @property string $firstname
 * @property string $secondname
 * @property string $patronymic
 *
 * @property Document[] $documents
 * @property Document[] $documents0
 * @property Document[] $documents1
 */
class People extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'secondname', 'patronymic'], 'required'],
            [['firstname', 'secondname', 'patronymic'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'secondname' => 'Secondname',
            'patronymic' => 'Patronymic',
        ];
    }

    /**
     * Gets query for [[Documents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Document::className(), ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Documents0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments0()
    {
        return $this->hasMany(Document::className(), ['register_id' => 'id']);
    }

    /**
     * Gets query for [[Documents1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments1()
    {
        return $this->hasMany(Document::className(), ['signed_id' => 'id']);
    }
}
