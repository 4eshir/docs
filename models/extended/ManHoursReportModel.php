<?php


namespace app\models\extended;


use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;

class ManHoursReportModel extends \yii\base\Model
{
    public $start_date;
    public $end_date;
    public $type;
    /*
     * 0 - человеко-часы
     * 1 - всего уникальных людей
     * 2 - всего людей
     */

    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['type'], 'safe'],
        ];
    }

    public function generateReport()
    {
        $result = '';
        foreach ($this->type as $oneType)
        {
            if ($oneType === '2')
            {
                $groups = TrainingGroupWork::find()->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                $parts = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groupsId])->andWhere(['status' => 0])->all();
                $result .= '<br>Количество всех обучающися, завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.': '.count($parts). ' чел.';
            }
        }
        return $result;
    }

    public function save()
    {
        return true;
    }
}