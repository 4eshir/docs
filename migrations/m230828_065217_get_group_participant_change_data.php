<?php

use yii\db\Migration;

/**
 * Class m230828_065217_get_group_participant_change_data
 */
class m230828_065217_get_group_participant_change_data extends Migration
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
        $this->update('get_group_participants_teacher_group', ['teacher_id' => 2], ['id' => 6]);
        $this->update('get_group_participants_teacher_group', ['teacher_id' => 5], ['id' => 7]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230828_065217_get_group_participant_change_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230828_065217_get_group_participant_change_data cannot be reverted.\n";

        return false;
    }
    */
}
