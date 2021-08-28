<?php


namespace app\models\extended;


use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\VisitWork;

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
        $result = '<table class="table table-bordered">';
        foreach ($this->type as $oneType)
        {
            if ($oneType === '0')
            {
                $lessons = TrainingGroupLessonWork::find()->where(['>=', 'lesson_date', $this->start_date])->andWhere(['<=', 'lesson_date', $this->end_date])->all();
                $lessonsId = [];
                foreach ($lessons as $lesson) $lessonsId[] = $lesson->id;
                $visit = VisitWork::find()->where(['IN', 'training_group_lesson_id', $lessonsId])->andWhere(['status' => 0])->all();
                $result .= '<tr><td>Количество человеко-часов за период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($visit).' ч/ч'.'</td></tr>';
            }
            if ($oneType === '1')
            {
                $groups = TrainingGroupWork::find()->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                $parts = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groupsId])->andWhere(['status' => 0])->all();
                $result .= '<tr><td>Количество уникальных обучающися, завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';
            }
            if ($oneType === '2')
            {
                $groups = TrainingGroupWork::find()->where(['>=', 'finish_date', $this->start_date])->andWhere(['<=', 'finish_date', $this->end_date])->all();
                $groupsId = [];
                foreach ($groups as $group) $groupsId[] = $group->id;
                $parts = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groupsId])->andWhere(['status' => 0])->all();
                $result .= '<tr><td>Количество всех обучающися, завершивших обучение в период с '.$this->start_date.' по '.$this->end_date.'</td><td>'.count($parts). ' чел.'.'</td></tr>';
            }
        }
        $result = $result.'</table>';
        return $result;
    }

    public function save()
    {
        return true;
    }
}