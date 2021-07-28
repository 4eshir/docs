<?php

namespace app\models\work;

use app\models\common\Branch;
use app\models\common\Nomenclature;
use Yii;

/**
 * This is the model class for table "nomenclature".
 *
 * @property int $id
 * @property string|null $number
 * @property string|null $name
 * @property int $branch_id
 *
 * @property Branch $branch
 */
class NomenclatureWork extends Nomenclature
{

    public function getFullNameWork()
    {
        return $this->number . ' ' . $this->name;
    }
}
