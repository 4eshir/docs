<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "certificat_templates".
 *
 * @property int $id
 * @property string $name
 * @property string $path
 *
 * @property Certificat[] $certificats
 */
class CertificatTemplates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'certificat_templates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'path'], 'required'],
            [['name', 'path'], 'string', 'max' => 1000],
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
            'path' => 'Path',
        ];
    }

    /**
     * Gets query for [[Certificats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCertificats()
    {
        return $this->hasMany(Certificat::className(), ['certificat_template_id' => 'id']);
    }
}
