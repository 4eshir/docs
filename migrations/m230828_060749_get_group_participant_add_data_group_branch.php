<?php

use yii\db\Migration;

/**
 * Class m230828_060749_get_group_participant_add_data_group_branch
 */
class m230828_060749_get_group_participant_add_data_group_branch extends Migration
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
        //--Добавляем столбцы в таблицу get_group_participants_training_group--
        $this->addColumn('get_group_participants_training_group', 'branch_id', $this->integer()->null());
        $this->addColumn('get_group_participants_training_group', 'budget', $this->integer()->null());
        //---------------------------------------------------------------------

        //--Редактируем данные в таблице get_group_participants_training_group--
        $this->update('get_group_participants_training_group', ['branch_id' => 2], ['id' => 1]);
        $this->update('get_group_participants_training_group', ['branch_id' => 1], ['id' => 2]);
        $this->update('get_group_participants_training_group', ['branch_id' => 3], ['id' => 3]);
        $this->update('get_group_participants_training_group', ['branch_id' => 3], ['id' => 4]);
        $this->update('get_group_participants_training_group', ['branch_id' => 5], ['id' => 5]);

        $this->update('get_group_participants_training_group', ['budget' => 1], ['id' => 1]);
        $this->update('get_group_participants_training_group', ['budget' => 1], ['id' => 2]);
        $this->update('get_group_participants_training_group', ['budget' => 1], ['id' => 3]);
        $this->update('get_group_participants_training_group', ['budget' => 0], ['id' => 4]);
        $this->update('get_group_participants_training_group', ['budget' => 0], ['id' => 5]);
//-----------------------------------------------------------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('get_group_participants_training_group', 'branch_id');
        $this->dropColumn('get_group_participants_training_group', 'budget');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230828_060749_get_group_participant_add_data_group_branch cannot be reverted.\n";

        return false;
    }
    */
}
