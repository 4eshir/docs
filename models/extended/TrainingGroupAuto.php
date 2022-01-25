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
    public $control_type_id;

    public $auds;


    public function rules()
    {
        return [
            [['start_time', 'end_time', 'auditorium'], 'required'],
            [['start_time', 'end_time'], 'string'],
            [['duration', 'control_type_id'], 'integer'],
            [['auds', 'day'], 'safe'],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
        ];
    }

    public function getDaysInRange($dateFromString, $dateToString)
    {
        $dateFrom = new \DateTime($dateFromString);
        $dateFromT = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];

        if ($dateFrom > $dateTo) {
            return $dates;
        }
        $date = explode("-", $dateFromString);
        $wint = date("w", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
        if ($wint == $this->day)
            array_push($dates, $dateFrom->format('Y-m-d'));


        $dates[] = $dateFrom->format('Y-m-d');

        $day = 'next monday';
        foreach ($this->day as $oneDay)
        {
            if ($oneDay === "0") $day = 'next monday';
            if ($oneDay === "1") $day = 'next tuesday';
            if ($oneDay === "2") $day = 'next wednesday';
            if ($oneDay === "3") $day = 'next thursday';
            if ($oneDay === "4") $day = 'next friday';
            if ($oneDay === "5") $day = 'next saturday';
            if ($oneDay === "6") $day = 'next sunday';

            $dateFromT->modify($day);
            while ($dateFromT <= $dateTo) {
                $dates[] = $dateFromT->format('Y-m-d');
                $dateFromT->modify('+1 week');
            }
            if ($dates[0] == $dates[1])
                unset($dates[0]);

            $dateFromT = new \DateTime($dateFromString);
        }

        return $dates;
    }
}