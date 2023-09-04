<?php

use yii\db\Migration;

/**
 * Class m230817_055031_update_participant_achievement
 */
class m230817_055031_update_participant_achievement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('participant_achievement', 'team_name_id', $this->integer()->null());

        $this->addForeignKey('key1_participant_achievement',
            'participant_achievement', 'team_name_id',
            'team_name', 'id',
            'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key1_participant_achievement', 'participant_achievement');
        $this->dropColumn('participant_achievement', 'team_name_id');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230817_055031_update_participant_achievement cannot be reverted.\n";

        return false;
    }
    */
}
