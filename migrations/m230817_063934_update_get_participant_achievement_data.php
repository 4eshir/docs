<?php

use app\models\work\ParticipantAchievementWork;
use yii\db\Migration;

/**
 * Class m230817_063934_update_get_participant_achievement_data
 */
class m230817_063934_update_get_participant_achievement_data extends Migration
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
        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 8,
            'teacher_participant_id' => null,
            'winner' => ParticipantAchievementWork::WINNER,
            'team_name_id' => 1,
        ]);

        $this->insert('get_participant_achievements_participant_achievement', [
            'id' => 9,
            'teacher_participant_id' => null,
            'winner' => ParticipantAchievementWork::PRIZE,
            'team_name_id' => 2,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 8]);
        $this->delete('get_participant_achievements_participant_achievement', ['id' => 9]);

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230817_063934_update_get_participant_achievement_data cannot be reverted.\n";

        return false;
    }
    */
}
