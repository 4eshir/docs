<?php

use yii\db\Migration;

/**
 * Class m230816_103143_get_participants_team_architecture_update_data
 */
class m230816_103143_get_participants_team_architecture_update_data extends Migration
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
        //--Добавляем данные в таблицу get_participants_team_name--
        $this->insert('get_participants_team_name', [
            'id' => 1,
            'name' => 'Team 1',
            'foreign_event_id' => 1
        ]);

        $this->insert('get_participants_team_name', [
            'id' => 2,
            'name' => 'Team 2',
            'foreign_event_id' => 3,
        ]);
        //---------------------------------------------------------

        //--Исправляем строку в get_participants_team--
        $this->update('get_participants_team', ['teacher_participant_id' => 7], ['id' => 4]);
        //---------------------------------------------

        //--Редактируем данные в таблице get_participants_team--
        $this->update('get_participants_team', ['team_name_id' => 1], ['id' => 1]);
        $this->update('get_participants_team', ['team_name_id' => 1], ['id' => 3]);
        $this->update('get_participants_team', ['team_name_id' => 2], ['id' => 4]);
        $this->update('get_participants_team', ['team_name_id' => 2], ['id' => 5]);
        //------------------------------------------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230816_103143_get_participants_team_architecture_update_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230816_103143_get_participants_team_architecture_update_data cannot be reverted.\n";

        return false;
    }
    */
}
