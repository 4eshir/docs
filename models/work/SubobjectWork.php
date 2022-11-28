<?php


namespace app\models\work;

use app\models\common\Subobject;
use Yii;


class SubobjectWork extends Subobject
{

	public function getStateString()
	{
		return $this->state == 0 ? 'Нерабочий' : 'Рабочий';
	}
}