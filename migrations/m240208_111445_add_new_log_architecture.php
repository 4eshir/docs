<?php

use yii\db\Migration;

/**
 * Class m240208_111445_add_new_log_architecture
 */
class m240208_111445_add_new_log_architecture extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //--создание таблиц--
        $this->createTable('o_log', [
            'id' => $this->primaryKey(),
            'text' => $this->string(1000),
            'user_id' => $this->integer(),
            'log_type_id' => $this->integer(),
        ]);

        $this->createTable('olog_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100),
        ]);

        $this->createTable('o_item', [
            'id' => $this->primaryKey(),
            'tablename' => $this->string(1000),
            'item_id' => $this->integer(),
        ]);

        $this->createTable('olog_oitem', [
            'id' => $this->primaryKey(),
            'log_id' => $this->integer(),
            'item_id' => $this->integer(),
        ]);

        $this->createTable('olog_transaction', [
            'id' => $this->primaryKey(),
            'current_log_id' => $this->integer(),
            'next_log_id' => $this->integer(),
        ]);

        $this->createTable('o_data', [
            'id' => $this->primaryKey(),
            'key' => $this->string(50),
            'value' => $this->string(1000),
        ]);

        $this->createTable('olog_odata', [
            'id' => $this->primaryKey(),
            'log_id' => $this->integer(),
            'data_id' => $this->integer(1000),
        ]);
        //-------------------

        //--создание внешних ключей--
        $this->addForeignKey('key1_olog',
            'o_log', 'type_id',
            'olog_type', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key1_olog_transaction',
            'olog_transaction', 'current_log_id',
            'o_log', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key2_olog_transaction',
            'olog_transaction', 'next_log_id',
            'o_log', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key1_olog_data',
            'olog_data', 'log_id',
            'o_log', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key2_olog_data',
            'olog_data', 'data_id',
            'o_data', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key1_olog_item',
            'olog_item', 'log_id',
            'o_log', 'id',
            'RESTRICT', 'RESTRICT');

        $this->addForeignKey('key2_olog_item',
            'olog_item', 'item_id',
            'o_item', 'id',
            'RESTRICT', 'RESTRICT');
        //---------------------------
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('key1_olog', 'o_log');
        $this->dropForeignKey('key1_olog_transaction', 'olog_transaction');
        $this->dropForeignKey('key2_olog_transaction', 'olog_transaction');
        $this->dropForeignKey('key1_olog_data', 'olog_data');
        $this->dropForeignKey('key2_olog_data', 'olog_data');
        $this->dropForeignKey('key1_olog_item', 'olog_item');
        $this->dropForeignKey('key2_olog_item', 'olog_item');

        $this->dropTable('o_log');
        $this->dropTable('olog_type');
        $this->dropTable('o_item');
        $this->dropTable('olog_oitem');
        $this->dropTable('olog_transaction');
        $this->dropTable('o_data');
        $this->dropTable('olog_odata');

        return true;
    }
}
