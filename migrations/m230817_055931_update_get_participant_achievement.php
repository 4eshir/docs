<?php

use yii\db\Migration;

/**
 * Class m230817_055931_update_get_participant_achievement
 */
class m230817_055931_update_get_participant_achievement extends Migration
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
        $this->addColumn('get_participant_achievements_participant_achievement', 'team_name_id', $this->integer()->null());

        $this->addForeignKey('key2_participant_achievement',
            'get_participant_achievements_participant_achievement', 'team_name_id',
            'get_participants_team_name', 'id',
            'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key2_participant_achievement', 'get_participant_achievements_participant_achievement');
        $this->dropColumn('get_participant_achievements_participant_achievement', 'team_name_id');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230817_055931_update_get_participant_achievement cannot be reverted.\n";

        return false;
    }
    */
}
