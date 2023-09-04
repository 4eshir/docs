<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "contract".
 *
 * @property int $id
 * @property string $date
 * @property string $number
 * @property string|null $file
 * @property int|null $contractor_id
 * @property string|null $key_words
 *
 * @property Company $contractor
 */
class Contract extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contract';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'number'], 'required'],
            [['date'], 'safe'],
            [['contractor_id'], 'integer'],
            [['number'], 'string', 'max' => 100],
            [['file', 'key_words'], 'string', 'max' => 1000],
            [['contractor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['contractor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'number' => 'Number',
            'file' => 'File',
            'contractor_id' => 'Contractor ID',
            'key_words' => 'Key Words',
        ];
    }

    /**
     * Gets query for [[Contractor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContractor()
    {
        return $this->hasOne(Company::className(), ['id' => 'contractor_id']);
    }
}
