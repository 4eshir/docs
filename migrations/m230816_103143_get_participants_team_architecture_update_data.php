<?php

use yii\db\Migration;

/**
 * Class m230816_103143_get_participants_team_architecture_update_data
 */
class m230816_103143_get_participants_team_architecture_update_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //--Редактируем данные в таблице get_participants_team--
        $this->update('get_participants_team', );
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
