<?php

use yii\db\Migration;

/**
 * Class m230830_121556_get_group_participant_add_cert
 */
class m230830_121556_get_group_participant_add_cert extends Migration
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
        //--Создание таблиц--
        $this->createTable('get_group_participants_certificat', [
            'id' => $this->primaryKey(),
            'certificat_number' => $this->integer(),
            'training_group_participant_id' => $this->integer(),
        ]);
        //-------------------

        //--Устанавливаем связи--
        $this->addForeignKey('key1_certificat',
            'get_group_participants_certificat', 'training_group_participant_id',
            'get_group_participants_training_group_participant', 'id',
            'RESTRICT', 'RESTRICT');
        //-----------------------

        //--Добавляем данные--
        $this->insert('get_group_participants_certificat', ['id' => 1, 'certificat_number' => 100, 'training_group_participant_id' => 1]);
        $this->insert('get_group_participants_certificat', ['id' => 2, 'certificat_number' => 100, 'training_group_participant_id' => 2]);
        $this->insert('get_group_participants_certificat', ['id' => 3, 'certificat_number' => 100, 'training_group_participant_id' => 4]);
        $this->insert('get_group_participants_certificat', ['id' => 4, 'certificat_number' => 100, 'training_group_participant_id' => 6]);
        $this->insert('get_group_participants_certificat', ['id' => 5, 'certificat_number' => 100, 'training_group_participant_id' => 9]);
        $this->insert('get_group_participants_certificat', ['id' => 6, 'certificat_number' => 100, 'training_group_participant_id' => 12]);
        $this->insert('get_group_participants_certificat', ['id' => 7, 'certificat_number' => 100, 'training_group_participant_id' => 13]);
        $this->insert('get_group_participants_certificat', ['id' => 8, 'certificat_number' => 100, 'training_group_participant_id' => 14]);
        $this->insert('get_group_participants_certificat', ['id' => 9, 'certificat_number' => 100, 'training_group_participant_id' => 15]);
        $this->insert('get_group_participants_certificat', ['id' => 10, 'certificat_number' => 100, 'training_group_participant_id' => 19]);
        $this->insert('get_group_participants_certificat', ['id' => 11, 'certificat_number' => 100, 'training_group_participant_id' => 21]);
        $this->insert('get_group_participants_certificat', ['id' => 12, 'certificat_number' => 100, 'training_group_participant_id' => 22]);
        $this->insert('get_group_participants_certificat', ['id' => 13, 'certificat_number' => 100, 'training_group_participant_id' => 23]);
        $this->insert('get_group_participants_certificat', ['id' => 14, 'certificat_number' => 100, 'training_group_participant_id' => 24]);
        $this->insert('get_group_participants_certificat', ['id' => 15, 'certificat_number' => 100, 'training_group_participant_id' => 25]);
        $this->insert('get_group_participants_certificat', ['id' => 16, 'certificat_number' => 100, 'training_group_participant_id' => 26]);
        $this->insert('get_group_participants_certificat', ['id' => 17, 'certificat_number' => 100, 'training_group_participant_id' => 27]);
        $this->insert('get_group_participants_certificat', ['id' => 18, 'certificat_number' => 100, 'training_group_participant_id' => 28]);
        $this->insert('get_group_participants_certificat', ['id' => 19, 'certificat_number' => 100, 'training_group_participant_id' => 29]);
        $this->insert('get_group_participants_certificat', ['id' => 20, 'certificat_number' => 100, 'training_group_participant_id' => 30]);
        //--------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key1_certificat', 'get_group_participants_certificat');

        $this->dropTable('get_group_participants_certificat');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_121556_get_group_participant_add_cert cannot be reverted.\n";

        return false;
    }
    */
}
