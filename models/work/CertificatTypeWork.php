<?php

namespace app\models\work;
use app\models\common\CertificatType;

use Yii;

class CertificatTypeWork extends CertificatType
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'certificat_type';
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

    
    
}
