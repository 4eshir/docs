<?php

use yii\db\Migration;

/**
 * Class m230928_120958_create_field_creator_and_editor
 */
class m230928_120958_create_field_creator_and_editor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // добавляем в исходящую документацию новое поле (последний редактор) изменяем регистратора на создателя карточки и добавляем ограничения внешнего ключа по юзеру
        $this->addColumn('document_out', 'last_edit_id', 'int(11)');
        $this->renameColumn('document_out', 'register_id', 'creator_id');
        $this->addForeignKey('document_out_ibfk_8',
            'document_out', 'last_edit_id',
            'user', 'id',
            'RESTRICT', 'RESTRICT');

        // добавляем во входящую документацию новое поле (последний редактор) изменяем регистратора на создателя карточки и добавляем ограничения внешнего ключа по юзеру
        $this->addColumn('document_in', 'last_edit_id', 'int(11)');
        $this->renameColumn('document_in', 'register_id', 'creator_id');
        $this->addForeignKey('document_in_ibfk_8',
            'document_out', 'last_edit_id',
            'user', 'id',
            'RESTRICT', 'RESTRICT');

        // добавляем в приказы новое поле (последний редактор) изменяем регистратора на создателя карточки и добавляем ограничения внешнего ключа по юзеру
        $this->addColumn('document_order', 'last_edit_id', 'int(11)');
        $this->renameColumn('document_order', 'register_id', 'creator_id');
        $this->addForeignKey('document_order_ibfk_6',
            'document_order', 'last_edit_id',
            'user', 'id',
            'RESTRICT', 'RESTRICT');

        // добавляем в положения новые поля: создатель и последний редкатор карточки, а также добавляем связку с таблицей юзер
        $this->addColumn('regulation', 'last_edit_id', 'int(11)');
        $this->addColumn('regulation', 'creator_id', 'int(11)');
        $this->addForeignKey('regulation_ibfk_3',
            'regulation', 'last_edit_id',
            'user', 'id',
            'RESTRICT', 'RESTRICT');
        $this->addForeignKey('regulation_ibfk_4',
            'regulation', 'creator_id',
            'user', 'id',
            'RESTRICT', 'RESTRICT');

        // добавляем в учете достижений новое поле: последний редкатор карточки, а также добавляем связку с таблицей юзер
        $this->addColumn('event', 'last_edit_id', 'int(11)');
        $this->addForeignKey('event_ibfk_11',
            'event', 'last_edit_id',
            'user', 'id',
            'RESTRICT', 'RESTRICT');

        // добавляем в учебные группы новое поле: последний редкатор карточки, а также добавляем связку с таблицей юзер
        $this->addColumn('training_group', 'last_edit_id', 'int(11)');
        $this->addForeignKey('training_group_ibfk_4',
            'training_group', 'last_edit_id',
            'user', 'id',
            'RESTRICT', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230928_120958_create_field_creator_and_editor cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230928_120958_create_field_creator_and_editor cannot be reverted.\n";

        return false;
    }
    */
}
