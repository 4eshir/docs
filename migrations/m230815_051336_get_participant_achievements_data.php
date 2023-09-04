<?php

use app\models\work\ParticipantAchievementWork;
use yii\db\Migration;

/**
 * Class m230815_051336_get_participant_achievements_data
 */
class m230815_051336_get_participant_achievements_data extends Migration
{
    public function init()
    {
        $this->db = Yii::$app->db_report_test;
        parent::init();
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //--Создаем тестовых победителей и призеров--
        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 1,
            'teacher_participant_id' => 1,
            'winner' => ParticipantAchievementWork::WINNER,
        ]);

        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 2,
            'teacher_participant_id' => 3,
            'winner' => ParticipantAchievementWork::PRIZE,
        ]);

        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 3,
            'teacher_participant_id' => 4,
            'winner' => ParticipantAchievementWork::PRIZE,
        ]);

        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 4,
            'teacher_participant_id' => 6,
            'winner' => ParticipantAchievementWork::WINNER,
        ]);

        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 5,
            'teacher_participant_id' => 8,
            'winner' => ParticipantAchievementWork::WINNER,
        ]);

        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 6,
            'teacher_participant_id' => 10,
            'winner' => ParticipantAchievementWork::WINNER,
        ]);

        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 7,
            'teacher_participant_id' => 11,
            'winner' => ParticipantAchievementWork::PRIZE,
        ]);
        //-------------------------------------------

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 1]);
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 2]);
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 3]);
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 4]);
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 5]);
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 6]);
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 7]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_051336_get_participant_achievements_data cannot be reverted.\n";

        return false;
    }
    */
}
