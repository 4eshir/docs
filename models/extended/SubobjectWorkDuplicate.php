<?php


namespace app\models\extended;

use app\models\common\Subobject;
use app\models\work\SubobjectWork;
use app\models\work\EntryWork;
use Yii;


class SubobjectWorkDuplicate extends Subobject
{
	public $subobjects;

	public function rules()
    {
        return [
            [['name', 'state'], 'required'],
            [['state', 'parent_id', 'entry_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['characteristics'], 'string', 'max' => 2000],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubobjectWork::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['entry_id'], 'exist', 'skipOnError' => true, 'targetClass' => EntryWork::className(), 'targetAttribute' => ['entry_id' => 'id']],
            ['subobjects', 'safe'],
        ];
    }

	public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'characteristics' => 'Characteristics',
            'state' => 'В рабочем состоянии',
            'parent_id' => 'Parent ID',
            'entry_id' => 'Entry ID',
        ];
    }

	public function getStateString()
	{
		return $this->state == 0 ? 'Нерабочий' : 'Рабочий';
	}
}