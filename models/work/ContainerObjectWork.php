<?php

namespace app\models\work;

use app\models\common\ContainerObject;
use app\models\null\MaterialObjectNull;
use Yii;


class ContainerObjectWork extends ContainerObject
{
	public function rules()
    {
        return [
            [['container_id', 'material_object_id'], 'integer'],
            [['container_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContainerWork::className(), 'targetAttribute' => ['container_id' => 'id']],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObjectWork::className(), 'targetAttribute' => ['material_object_id' => 'id']],
        ];
    }

	public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'container_id' => 'Container ID',
            'material_object_id' => 'Наименование объекта',
        ];
    }


	public function getMaterialObjectWork()
    {
        $try = $this->hasOne(MaterialObjectWork::className(), ['id' => 'material_object_id']);
        return $try->all() ? $try : new MaterialObjectNull();
    }
}
