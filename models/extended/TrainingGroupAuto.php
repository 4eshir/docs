<?php


namespace app\models\extended;


use app\models\common\Auditorium;
use yii\base\Model;

class TrainingGroupAuto extends Model
{
    public $day;
    public $start_time;
    public $end_time;
    public $auditorium_id;
    public $duration;

    public $auds;


    public function rules()
    {
        return [
            [['start_time', 'end_time', 'auditorium'], 'required'],
            [['day','start_time', 'end_time'], 'string'],
            [['duration'], 'integer'],
            [['auds'], 'safe'],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
        ];
    }

    public function getDaysInRange($dateFromString, $dateToString)
    {
        $dateFrom = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];

        if ($dateFrom > $dateTo) {
            return $dates;
        }
        $date = explode("-", $dateFromString);
        $wint = date("w", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
        if ($wint == $this->day)
            array_push($dates, $dateFrom->format('Y-m-d'));
        $day = 'next monday';

        if ($this->day == 2) $day = 'next tuesday';
        if ($this->day == 3) $day = 'next wednesday';
        if ($this->day == 4) $day = 'next thursday';
        if ($this->day == 5) $day = 'next friday';
        if ($this->day == 6) $day = 'next saturday';
        if ($this->day == 7) $day = 'next sunday';
        $dateFrom->modify($day);

        while ($dateFrom <= $dateTo) {
            $dates[] = $dateFrom->format('Y-m-d');
            $dateFrom->modify('+1 week');
        }
        if ($dates[0] == $dates[1])
            unset($dates[0]);
        return $dates;
    }
}